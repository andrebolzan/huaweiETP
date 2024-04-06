# huaweiETP
Retificadora gerenciável Huawei ETP48xx  - ETP4860, ETP4860, ETP4890, ETP48400, ETP48200

Na verdade foco está na interface de gerenciamento SMU (site monitor unit , Unidade de monitoramento de sítio/local) que é quem fornce as MIBs, 
Alguns modelos que usam a MIB SiteMonitor, a SMU11B e SMU11C ou SMU02B e SMU02C , as SMU com final C tem um slot de expansão para sensores e contatos secos. 

A retificadora Huawei gerenciável ETP48xx tem algumas variações desse modelo, com 2, 3 ate 6 modulos de 15A ate 75A , capacita máxima ate 450A

Ela pode ser mono faixa (fase neutro 110 ou 220) ou fase fase fase (220 e 380A) ou trifásica 380A com ou sem neutro.

Essa informação sobre tipo de alimentação é importante para SMU por temos como monitor cada fase do balanceamento da carga na parte AC

"iserir imagem"
O módulo SMU gerência x elementos infernos

Entrada AC
Monofase ou  trifase
Barramento DC
Fontes/Módulo DC
Bateria 
Balanceamento da carga 
Borns e alarmes

- Entrada AC

Vamos começar pela parte mais simples
O módulo AC, responsável pela entrada de energia. 
Apesar de ter poucos parâmetros para monitorar, é interessante definir essas configurações na interface web para que alguns alertas funcionem.
O mais  importante é especificar o tipo de alimentação, se é '1 way - 3 way' (monofásico ou trifásico), bem como a faixa de tensão mínima e máxima.
Essas informações são essenciais para o funcionamento dos alertas de status operacional e detecção de falta de fase (aplicável apenas em sistemas trifásicos).
O parâmetro o Status Operacional é a estrala desse modulo é quem diz se tem ou não energia no site/POP ou em modelo trifasico se alguma fase esta com problema.

Coletamos  como estatística Tensão (voltagem) e corrente (amperagem)
Não consegui coletar os hertz, a frequência fica próxima versão, pois preciso do apoiada Huawei nesse ponto.

2) 
Barramento DC , famigerado BarBus (BB para os íntimos) o barrento de ônibus, tradução é feia, mas conceito é simple, é um Barramento que vai do começo ao fim (igual barra de segurança dos ônibus urbanos)


Ele interliga as fontes DC a carga/load (os equipamentos conectados) e a conexão(born) da bateria … 
Destaque que bateria esta conectada no BarBus , então não temos o monitoramento “efetivo” da Bateria, ela é só mais “uma carga” no barramento.

Não fizemos ajustes aqui, apenas coleta de dados e definir parâmetros “normal” ou “alerta”.

Esse modulo tem parâmetros importantes que é o Status Operacional, caso as duas retificado AC/DC e a bateria falhe ele vai retornar erro. Se isso acontecer você tem “grande problema nas mãos”

Coletamos Status Operacional, Tensão(voltagem), Total da Corrente (voltagem), Total da Carga em Watts e 

3) 
Fonte DC , retificadora ou módulo DC, qualquer nome cabe aqui.

Aqui não temos parâmetro para ajustar aqui, somente campo informativo de ‘alerta’ 

Aqui localizamos tensão/voltagem corrente individual dos módulos, Status Operacional que retorna motivo da falha de cada fonte/módulo.


Coletamos, Tensão , corrente , temperatura e Status Operacional de maneira individual com prefixo F01 , F02 , F0X


4)
Bateria (String Bateria - serie de bateria)

Aqui é onde fica pulo do gato, ajuste de tensão/voltagem do Barramento DC (Bar BUS)  é feito na bateria.

Mas porque ? Porque ela é o “elemento mais frágil” do barramento, aquele que pode explodir, daninfir, incendiar o site, ele tem que ser tratado com toda atenção necessária.

Existe outro pulo do gato, se você usa 2 retificado ETP4xxx não 2 modulo em 1 retificado, você pode ajustar balanceamento da Carga (load) aumentado tensão no modulo com a menor Carga, qualquer balamento menor que 10% é considerado perfeito.

Temos status de bateria em carregamento, descarga ou ‘booster”, uma “choque” que a retificadora da na bateria de tempos em tempos, não ficou claro como esse processo é iniciado na documentação da Huawei, assim que tiver novidade, volto e atualizo documento.

Aqui precisamos declarar a capacidade(tamanho) do banco em Amperes horas (Ah) , pois ele é usado no cálculo da autonomia.

Autonomia, aqui a retificado faz uma conta da Carga atual, tensão do barramento DC (Bar Bus) e a capacidade da bateria, EX 
Tensao BB: 56
Carga: 10A
Bateria 100A
(56*100) / (56*10)  = 10 horas 
Esse é só exemplo didático, retificado leva inúmeros outros fatores, temperatura, tensão da bateria que vai caindo, fator de eficiência, etc

Tensão (voltagem de corte) esse é parâmetro que você define qual vai ser a descarga da bateria, em bateria de chumbo ácido normalmente até 44V (11 voltes por bateria), sempre bom consultar fabricante para saber qual parâmetro informa aqui.

Corrente(amperagem) da bateria normalmente vai ficar em 0, pois ela está em flutuação, só vai ser alterar quando ela estiver em descarga retornando número  negativo ou carregando que retorna número positivo.

Percentual de carga, retorna percentual (% ) de carga da bateria.


5)
Born de alarme, tem 2 conjuntos os auxiliares e os de ambiente
No auxiliar tem vários contatos seco pra ser usado em “porta aberta”, sensor de fumaça, sensor de água, sinal de gerador partido, etc , sinais Verdadeiro e falso.

No segundo conjuntos de sensores temos os 2 mais importante são os de tensão e da temperatura da bateria, esses são usados para compensação

Ainda não consegui achar o modelo do sensor certo,  se é NTC, PTC, se é de 10k ou 20k, está em desenvolvimento, assim que tiver as informações volto e atualizo o documento.

Os 2 borns para verificação da bateria devem ser instalado “no meio da bateria”, ainda não consegui essa informação, se é no polo de 24v e 36v ou em 0(zero) e 48V, assim que tiver as informações volto e atualizo o documento.

Ainda não implementado.


Proxima Versão

Coletar hertz (frequência), que no Brasil é de 60Hz, na concessionária isso é “irrelevante”  na maior parte do tempo, mas normalmente um “problema” em geradores menores, principalmente os a gasolina que tende a ter grande variação.
