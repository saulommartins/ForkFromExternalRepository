<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe Carne de Dados Cadastrais Mata de Sao Joao
    * Data de Criação: 31/10/2006

    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Componentes

    * $Id: RCarneDadosCadastraisMataSaoJoao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.11
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once 'RCarneDiversosPetropolis.class.php';
define       ('FPDF_FONTPATH','/var/www/sw1/gestaoAdministrativa/fontes/PHP/FPDF/fonts/');

class RCarneDadosCadastraisMataSaoJoao extends RCarneDiversosPetropolis
{
    /**
     * @access public
     * @var String Texto de Composição do Calculo
     */
    public $stComposicaoCalculo ;
    /**
     * @access public
     * @var String
     */
    public $stNomePrefeitura ;
    /**
     * @access public
     * @var String
     */
    public $stSubTitulo ;
    /**
     * @access public
     * @var String
     */
    public $stExercicio ;
    /**
     * @access public
     * @var String
     */
    public $stContribuinte ;
    /**
     * @access public
     * @var String
     */
    public $stInscricaoCadastral ;
    /**
     * @access public
     * @var String
     */
    public $stCategoriaUtilizacao ;
    /**
     * @access public
     * @var String
     */
    public $stTipoTributo ;
    /**
     * @access public
     * @var String
     */
    public $stCodigoLogradouro ;
    /**
     * @access public
     * @var String
     */
    public $stNomeLogradouro ;
    /**
     * @access public
     * @var String
     */
    public $stComplemento ;
    /**
     * @access public
     * @var String
     */
    public $stQuadra ;
    /**
     * @access public
     * @var String
     */
    public $stLote ;
    /**
     * @access public
     * @var String
     */
    public $stDistrito ;
    /**
     * @access public
     * @var String
     */
    public $stRegiao ;
    /**
     * @access public
     * @var String
     */
    public $stCep ;
    /**
     * @access public
     * @var String
     */
    public $stCidade ;
    /**
     * @access public
     * @var String
     */
    public $stEstado ;
    /**
     * @access public
     * @var String
     */
    public $stAreaUsoPrivativoTerreno ;
    /**
     * @access public
     * @var String
     */
    public $stVupt ;
    /**
     * @access public
     * @var String
     */
    public $stVupc;
    /**
     * @access public
     * @var String
     */
    public $stValorVenalTerreno;
    /**
     * @access public
     * @var String
     */
    public $stImpostoTerritorial;
    /**
     * @access public
     * @var String
     */
    public $stAreaUsoPrivativoEdificacao;
    /**
     * @access public
     * @var String
     */
    public $stValorVenalConstrucao;
    /**
     * @access public
     * @var String
     */
    public $stImpostoPredial;
    /**
     * @access public
     * @var String
     */
    public $stAliquota;
    /**
     * @access public
     * @var String
     */
    public $stValorVenalImovel;
    /**
     * @access public
     * @var String
     */
    public $stValorImposto;
    /**
     * @access public
     * @var String
     */
    public $stTipoUnidade;
    /**
     * @access public
     * @var String
     */
    public $stZona;
    /**
     * @access public
     * @var String
     */
    public $stAreaM2;
    /**
     * @access public
     * @var String
     */
    public $stValorM2;
    /**
     * @access public
     * @var String
     */
    public $stValorTaxa;
    /**
     * @access public
     * @var String
     */
    public $stValorTotalTributos;
    /**
     * @access public
     * @var String
     */
    public $stContribIlumPublica;
    /**
     * @access public
     * @var String
     */
    public $arDemonstrativoParcelas;
    /**
     * @access public
     * @var String
     */
    public $arVencimentosDemonstrativos;

    public $stCamLogo;

    public function RCarneDadosCadastraisMataSaoJoao()
    {
        ;

        parent::RCarneDiversosPetropolis();
        /**
         * Seta Informações Basicas da Prefeitura
            */
        $this->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->stSubTitulo = 'Secretaria de Administração e Finanças';
        $this->lblMulta = '(+) MULTA DE MORA';
        $this->lblJuros = '(+) JUROS DE MORA';
        $this->lblOutros = '(+) ATUALIZAÇÃO MONETÁRIA';

        /**
         * Seta Configuração do PDF
         */
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
        $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );

        $stNomeImagem = $rsListaImagens->getCampo("valor");
        $this->stCamLogo = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'.$stNomeImagem;
    }
    public function desenhaCarne($x , $y)
    {
$stComposição =
"\n                                                                                                        COMPOSIÇÃO DO CÁLCULO\n
\n
I- Imposto Sobre a Propriedade Predial e Territorial Urbana (IPTU) => Valor do Imposto = Valor Venal x Alíquota\n
   Valor Venal = Valor do Terreno (VUPt x área m2) + Valor da Edificação (VUPC x área m2).\n
\n
II- Taxa de Limpeza Pública =>  Valor da Taxa = Área (do terreno ou da construção) x valor p/m2\n
\n
III- Contribuição de Iluminação Pública => Área do Terreno x R$0,05 (cobrada no carnê de IPTU apenas para os imóveis sem edificação)\n
\n
Observações:\n
1) VUPt é o Valor do metro quadrado do terreno e VUPc o valor do metro quadrado da construção;\n
(estes valores são definidos a Planta Genérica de Valores do Município, Lei n.281/2006)\n
\n
2) O recolhimento fora do prazo enseja a cobrança dos seguintes acréscimos, calculados sobre o valor atualizado monetariamente:\n
  - Multa de mora de 5 % (cinco por cento), se o tributo for pago no prazo de 30 (trinta)dias, após o vencimento; 10% (dez por cento), se o atraso for\n .
  superior a 30(trinta) dias, e até 60 (sessenta) dias; 15% (quinze por cento), se o atraso for superior a 60 (sessenta) dias.\n
  - Juros de mora de 1% (um por cento) ao mes calendário ou fração, a razão de 0,033% ao dia limitando ao máximo de 1%, calculado á data do seu\n
  pagamento. (fundamentação Legal: Inciso IV, Artigo.60 da Lei n.280/2006).\n
  - Multa de Infração quando for o caso. (fundamentação Legal: Inciso II, Artigo.60 da Lei n.280/2006);\n
\n
3) Os tributos poderão ser pagos em até 10 cotas, com valor mínimo de R$ 10,00 cada (Fundamentação Legal: Art.2º do Decreto n. 332/2006);\n
\n
4) Necessitando alterar os dados cadastrais compareça, munido de documento comprobatório, ao Setor de Tributos e Fiscalização, órgão da Secretária\n
Municipal de Administração e Finanças, sito na Rua Santos Dummont, n. 86 - Centro - CEP 40280-000 - Mata de São João /BA.\n
\n";

        $inTamY = 1.15;

        $this->SetXY($x+10,$y+10);
        $this->stComposicaoCalculo = $stComposição;
        $this->SetXY($x+10,$y+10);
        /* Inicializa Fonte*/
        $this->setFont( 'Arial','',10 );

        /**
         * Retangulos
         */
        /* Retangulo da Composicação */
        $this->Rect( $x, $y, 189, 70*$inTamY );
        /* Retangulo Dados Cadastrais */
        $this->Rect( $x, $y + (78*$inTamY) , 189, 69*$inTamY );

        /* Composição do Calculo */
        $this->setFont( 'Arial','',7 );
        $this->setLeftMargin(10);
        $this->Write( 1.3 , $this->stComposicaoCalculo );
        $this->setLeftMargin(0);

        /**
         * Montar Estrutura dos Dados Cadastrais
         */

        /* Linhas Horizontais */
        $this->Line( $x , $y +  (90*$inTamY) , $x +189, $y +  (90*$inTamY) );
        $this->Line( $x , $y +  (98*$inTamY) , $x +189, $y +  (98*$inTamY) );
        $this->Line( $x , $y + (102*$inTamY) , $x +189, $y + (102*$inTamY) );
        $this->Line( $x , $y + (107*$inTamY) , $x +189, $y + (107*$inTamY) );
        $this->Line( $x , $y + (113*$inTamY) , $x +189, $y + (113*$inTamY) );
        $this->Line( $x , $y + (116*$inTamY) , $x +134, $y + (116*$inTamY) );
        $this->Line( $x , $y + (121*$inTamY) , $x +189, $y + (121*$inTamY) );
        $this->Line( $x , $y + (128*$inTamY) , $x +134, $y + (128*$inTamY) );
        $this->Line( $x , $y + (133*$inTamY) , $x +134, $y + (133*$inTamY) );
        $this->Line( $x , $y + (136*$inTamY) , $x +134, $y + (136*$inTamY) );
        $this->Line( $x , $y + (141*$inTamY) , $x +134, $y + (141*$inTamY) );

        /* Linhas Verticais */

        /* Linha Ao lado do demonstrativo, aquela maior */
        $this->Line( $x + 134 , $y +  (113*$inTamY), $x + 134 , $y  + (147*$inTamY));

        /* Linha 1*/
        $this->Line( $x + 88 , $y +  (78*$inTamY) , $x + 88 , $y  +  (90*$inTamY));
        /* Linha 2*/
        $this->Line( $x +  64 , $y +  (90*$inTamY) , $x +  64 , $y  +  (98*$inTamY));
        $this->Line( $x +  91 , $y +  (90*$inTamY) , $x +  91 , $y  +  (98*$inTamY));
        $this->Line( $x + 136 , $y +  (90*$inTamY) , $x + 136 , $y  +  (98*$inTamY));
        /* Linha 3*/
        /* Linha 4*/
        $this->Line( $x +  34 , $y + (102*$inTamY) , $x + 34 , $y  + (107*$inTamY));
        //$this->Line( $x +  64 , $y + 102 , $x + 64 , $y  + 107);
        $this->Line( $x + 105 , $y + (102*$inTamY) , $x + 105 , $y  + (107*$inTamY));
        /* Linha 5*/
        $this->Line( $x +  25  , $y + (107*$inTamY) , $x +  25 , $y  + (113*$inTamY));
        $this->Line( $x +  44  , $y + (107*$inTamY) , $x +  44 , $y  + (113*$inTamY));
        $this->Line( $x +  81  , $y + (107*$inTamY) , $x +  81 , $y  + (113*$inTamY));
        $this->Line( $x + 105  , $y + (107*$inTamY) , $x + 105 , $y  + (113*$inTamY));
        $this->Line( $x + 127  , $y + (107*$inTamY) , $x + 127 , $y  + (113*$inTamY));
        $this->Line( $x + 163  , $y + (107*$inTamY) , $x + 163 , $y  + (113*$inTamY));
        /* Linha 7*/
        $this->Line( $x +  25  , $y + (115*$inTamY) , $x +  25 , $y  + (121*$inTamY));
        $this->Line( $x +  64  , $y + (115*$inTamY) , $x +  64 , $y  + (121*$inTamY));
        $this->Line( $x +  76  , $y + (115*$inTamY) , $x +  76 , $y  + (121*$inTamY));
        $this->Line( $x + 108  , $y + (115*$inTamY) , $x + 108 , $y  + (121*$inTamY));
        /* Linha 8*/
        $this->Line( $x +  25  , $y + (121*$inTamY) , $x +  25 , $y  + (128*$inTamY));
        $this->Line( $x +  64  , $y + (121*$inTamY) , $x +  64 , $y  + (128*$inTamY));
        $this->Line( $x +  76  , $y + (121*$inTamY) , $x +  76 , $y  + (128*$inTamY));
        $this->Line( $x + 110  , $y + (121*$inTamY) , $x + 110 , $y  + (128*$inTamY));
        /* Linha 9*/
        $this->Line( $x +  25  , $y + (128*$inTamY) , $x +  25 , $y  + (133*$inTamY) );
        $this->Line( $x +  52  , $y + (128*$inTamY) , $x +  52 , $y  + (133*$inTamY) );
        $this->Line( $x +  98  , $y + (128*$inTamY) , $x +  98 , $y  + (133*$inTamY) );
        /* Linha 11*/
        $this->Line( $x +  21  , $y + (136*$inTamY) , $x +  21 , $y  + (141*$inTamY) );
        $this->Line( $x +  39  , $y + (136*$inTamY) , $x +  39 , $y  + (141*$inTamY) );
        $this->Line( $x +  56  , $y + (136*$inTamY) , $x +  56 , $y  + (141*$inTamY) );
        $this->Line( $x +  71  , $y + (136*$inTamY) , $x +  71 , $y  + (141*$inTamY) );
        $this->Line( $x +  92  , $y + (133*$inTamY) , $x +  92 , $y  + (141*$inTamY) );
        /* Linha 12 */
        $this->Line( $x +  95  , $y + (141*$inTamY) , $x +  95 , $y  + (147*$inTamY) );

        /**
         * Titulos dos Dados
         */
        /* imagem*/
        $stExt = substr( $this->stCamLogo, strlen($this->stCamLogo)-3, strlen($this->stCamLogo) );
        $this->Image( $this->stCamLogo , $x+1 , $y+(79*$inTamY) , 10 , 10 , $stExt);
        /* dados */
        $this->setFont( 'Arial','',14 );
        $this->Text( $x+112 , $y+(84*$inTamY) , 'DADOS CADASTRAIS' );
        /* exercicio */
        $this->setFont( 'Arial','',10 );
        $this->Text( $x+122 , $y+(88*$inTamY) , 'EXERCÍCIO  ' . $this->stExercicio );

        $this->setFont( 'Arial','',5 );
        $this->Text( $x+  1 , $y+(92*$inTamY) , 'CONTRIBUINTE:' );
        $this->Text( $x+ 65 , $y+(92*$inTamY) , 'INSCRIÇÃO CADASTRAL:' );
        $this->Text( $x+ 92 , $y+(92*$inTamY) , 'CATEGORIA DE UTILIZAÇÃO DO IMÓVEL:' );
        $this->Text( $x+137 , $y+(92*$inTamY) , 'TIPO DE TRIBUTO(ESPECIFICAÇÃO):' );

        $this->setFont( 'Arial','',8 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(98*$inTamY) , 189 , 4*$inTamY , 'DF');
        $this->Text( $x+  72 , $y+(101*$inTamY) , 'E N D E R E Ç O  D O  I M Ó V E L' );

        $this->setFont( 'Arial','',5 );

        $this->Text( $x+    1 , $y+(104*$inTamY) , 'CÓDIGO DO LOGRADOURO:' );
        $this->Text( $x+   35 , $y+(104*$inTamY) , 'NOME DO LOGRADOURO:' );
        $this->Text( $x+  106 , $y+(104*$inTamY) , 'COMPLEMENTO:' );

        $this->Text( $x+    1 , $y+(109*$inTamY) , 'QUADRA:' );
        $this->Text( $x+   26 , $y+(109*$inTamY) , 'LOTE:' );
        $this->Text( $x+   45 , $y+(109*$inTamY) , 'DISTRITO:' );
        $this->Text( $x+   82 , $y+(109*$inTamY) , 'REGIÃO:' );
        $this->Text( $x+  106 , $y+(109*$inTamY) , 'CEP:' );
        $this->Text( $x+  128 , $y+(109*$inTamY) , 'CIDADE:' );
        $this->Text( $x+  164 , $y+(109*$inTamY) , 'ESTADO:' );

        $this->setFont( 'Arial','',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(113*$inTamY) , 134 , 3*$inTamY , 'DF');
        $this->Text( $x+1 , $y+(115.2*$inTamY) , ' D A D O S   D O   I P T U   -   I M P O S T O   S O B R E   A   P R O P R I E D A D E   P R E D I A L   E   T E R R I T O R I A L   U R B A N A' );

        $this->setFont( 'Arial','B',5 );
        $this->Text( $x+    4 , $y+(118*$inTamY) , 'DADOS SOBRE O' );
        $this->Text( $x+    1 , $y+(120*$inTamY) , 'TERRENO OU ÁREA UTIL' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   26 , $y+(118*$inTamY) , 'ÁREA DE USO Privativo + Fração Idel (M2):' );
        $this->Text( $x+   65 , $y+(118*$inTamY) , 'VUPt(R$):' );
        $this->Text( $x+   78 , $y+(118*$inTamY) , 'VALOR VENAL DO TERRENO(R$):' );
        $this->Text( $x+  109 , $y+(118*$inTamY) , 'IMPOSTO TERRITORIAL(R$):' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+  135 , $y+(115*$inTamY) , 'DESCONTO NA COTA ÚNICA TAXA LIXO(%): 10,00' );
        $this->Text( $x+  135 , $y+(117*$inTamY) , 'DESCONTO NA COTA ÚNICA IPTU(%): 10,00' );
        $this->Text( $x+  135 , $y+(119*$inTamY) , 'CONTRIBUIÇÃO DE ILUMINAÇÃO PÚBLICA(CIP) = M2 X R$ 0,05' );

        $this->setFont( 'Arial','B',5 );
        $this->Text( $x+    4 , $y+(123*$inTamY) , 'DADOS SOBRE A' );
        $this->Text( $x+    5 , $y+(125*$inTamY) , 'EDIFICAÇÃO' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   26 , $y+(123*$inTamY) , 'ÁREA DE USO Privativo + Fração Idel (M2):' );
        $this->Text( $x+   65 , $y+(123*$inTamY) , 'VUPc(R$):' );
        $this->Text( $x+   78 , $y+(123*$inTamY) , 'VALOR VENAL DA CONTRUÇÃO(R$):' );
        $this->Text( $x+  111 , $y+(123*$inTamY) , 'IMPOSTO PREDIAL(R$):' );

        $this->setFont( 'Arial','B',5 );
        $this->Text( $x+  4.5 , $y+(130*$inTamY) , 'COMPOSIÇÃO' );
        $this->Text( $x+    5 , $y+(132*$inTamY) , 'DO TRIBUTO' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   26 , $y+(130*$inTamY) , 'ALIQUOTA:' );
        $this->Text( $x+   53 , $y+(130*$inTamY) , 'VALOR VENAL DO IMÓVEL:' );
        $this->Text( $x+   99 , $y+(130*$inTamY) , 'VALOR DO IMPOSTO:' );

        $this->setFont( 'Arial','',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(133*$inTamY) , 92 , 3 , 'DF');
        $this->Rect( $x+92, $y+(133*$inTamY) , 42 , 3 , 'DF');
        $this->Text( $x+18 , $y+(135*$inTamY) , ' D A D O S   D A   T A X A   D E   L I M P E Z A   P U B L I C A ' );
        $this->Text( $x+94 , $y+(135*$inTamY) , 'CONTRIBUIÇÃO DE ILUMINAÇÃO PUBLICA' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   1 , $y+(138*$inTamY) , 'TIPO DE UNIDADE:' );
        $this->Text( $x+  22 , $y+(138*$inTamY) , 'ZONA:' );
        $this->Text( $x+  40 , $y+(138*$inTamY) , 'ÁREA (M2):' );
        $this->Text( $x+  57 , $y+(138*$inTamY) , 'VALOR (M2):' );
        $this->Text( $x+  72 , $y+(138*$inTamY) , 'VALOR DA TAXA(R$):' );

        $this->setFont( 'Arial','B',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x+134, $y+(121*$inTamY) , 55 , 3*$inTamY , 'DF');
        $this->Text( $x+148 , $y+(123*$inTamY) , ' Demonstrativo das Parcelas' );

        $this->setFont( 'Arial','B',5 );
        $this->Text( $x+141 , $y+(126*$inTamY) , ' Parcela Única :' . $this->arVencimentosDemonstrativos[0] . ' ' . $this->arDemonstrativoParcelas[0]);

        /* parcelas */
        $this->setFont( 'Arial','',5 );
        $this->Text( $x+138 , $y+(129*$inTamY) , '01) ' . $this->arVencimentosDemonstrativos[1] . ' ' . $this->arDemonstrativoParcelas[1] );
        $this->Text( $x+138 , $y+(132*$inTamY) , '02) ' . $this->arVencimentosDemonstrativos[2] . ' ' . $this->arDemonstrativoParcelas[2] );
        $this->Text( $x+138 , $y+(135*$inTamY) , '03) ' . $this->arVencimentosDemonstrativos[3] . ' ' . $this->arDemonstrativoParcelas[3] );
        $this->Text( $x+138 , $y+(138*$inTamY) , '04) ' . $this->arVencimentosDemonstrativos[4] . ' ' . $this->arDemonstrativoParcelas[4] );
        $this->Text( $x+138 , $y+(141*$inTamY) , '05) ' . $this->arVencimentosDemonstrativos[5] . ' ' . $this->arDemonstrativoParcelas[5] );

        $this->Text( $x+165 , $y+(129*$inTamY) , '06) ' . $this->arVencimentosDemonstrativos[6] . ' ' . $this->arDemonstrativoParcelas[6] );
        $this->Text( $x+165 , $y+(132*$inTamY) , '07) ' . $this->arVencimentosDemonstrativos[7] . ' ' . $this->arDemonstrativoParcelas[7] );
        $this->Text( $x+165 , $y+(135*$inTamY) , '08) ' . $this->arVencimentosDemonstrativos[8] . ' ' . $this->arDemonstrativoParcelas[8] );
        $this->Text( $x+165 , $y+(138*$inTamY) , '09) ' . $this->arVencimentosDemonstrativos[9] . ' ' . $this->arDemonstrativoParcelas[9] );
        $this->Text( $x+165 , $y+(141*$inTamY) , '10) ' . $this->arVencimentosDemonstrativos[10] . ' ' . $this->arDemonstrativoParcelas[10] );

        $this->setFont( 'Arial','B',6 );
        $this->Text( $x+96 , $y+(143*$inTamY) , 'VALOR TOTAL DOS TRIBUTOS (R$):' );

        $this->setFont( 'Arial','',6 );
        $this->Text( $x+3 , $y+(143*$inTamY) , 'Quaisquer dúvidas, consulte o Setor de Tributos e Fiscalização,
na Rua Santos Dummont, 86,' );
        $this->Text( $x+6 , $y+(146*$inTamY) , 'Centro, CEP: 48280-000, Tel/Fax: (71) 3635-1669 / (71) 3635-3077.' );
        /* mostrar dados */

        $this->setFont( 'Arial','B',7 );
        $this->Text( $x+14 , $y+(85*$inTamY) , $this->stNomePrefeitura );
        $this->setFont( 'Arial','',6 );
        $this->Text( $x+14 , $y+(88*$inTamY) , $this->stSubTitulo );

        $this->setFont( 'Arial','',5 );
        /* contribuibte */
        $this->Text( $x+ 2 , $y+(96*$inTamY) , $this->stContribuinte );
        /* inscricao */
        $this->Text( $x+74 , $y+(96*$inTamY) , $this->stInscricaoCadastral );
        /* categoria */
        $this->Text( $x+107 , $y+(96*$inTamY) , $this->stCategoriaUtilizacao );
        /* tipo tributo */
        $this->Text( $x+138 , $y+(96*$inTamY) , $this->stTipoTributo );

        /* logradouro */
        $this->Text( $x+14 , $y+(106*$inTamY) , $this->stCodigoLogradouro );
        /* nome logradouro */
        $this->Text( $x+35 , $y+(106*$inTamY) , $this->stNomeLogradouro );
        /* complemento */
        $this->Text( $x+107 , $y+(106*$inTamY) , $this->stComplemento );

        /* quadra*/
        $this->Text( $x+ 14 , $y+(112*$inTamY) , $this->stQuadra );
        /* lote */
        $this->Text( $x+ 35 , $y+(112*$inTamY) , $this->stLote);
        /* distrito */
        $this->Text( $x+ 45 , $y+(112*$inTamY) , $this->stDistrito );
        /* regiao */
        $this->Text( $x+ 89 , $y+(112*$inTamY) , $this->stRegiao);
        /* cep */
        $this->Text( $x+108 , $y+(112*$inTamY) , $this->stCep);
        /* cidade */
        $this->Text( $x+129 , $y+(112*$inTamY) , $this->stCidade );
        /* estado */
        $this->Text( $x+170 , $y+(112*$inTamY) , $this->stEstado );

        /* uso privativo terreno */
        $this->Text( $x+ 43 , $y+(120*$inTamY) , $this->stAreaUsoPrivativoTerreno );
        /* vupt */
        $this->Text( $x+ 67 , $y+(120*$inTamY) , $this->stVupt);
        /* venal terreno */
        $this->Text( $x+ 89 , $y+(120*$inTamY) , $this->stValorVenalTerreno );
        /* imposto terre */
        $this->Text( $x+ 115, $y+(120*$inTamY) , $this->stImpostoTerritorial);
        /* uso privativo edificaoca */
        $this->Text( $x+ 43 , $y+(126*$inTamY) , $this->stAreaUsoPrivativoEdificacao);
        /* vupc */
        $this->Text( $x+ 67 , $y+(126*$inTamY) , $this->stVupc );
        /* venal constru */
        $this->Text( $x+ 89 , $y+(126*$inTamY) , $this->stValorVenalConstrucao );
        /* impostto predial */
        $this->Text( $x+115 , $y+(126*$inTamY) , $this->stImpostoPredial );

        /* aliquota */
        $this->Text( $x+ 36 , $y+(132*$inTamY) , $this->stAliquota );
        /* venal imovel */
        $this->Text( $x+ 71, $y+(132*$inTamY) , $this->stValorVenalImovel);
        /* imposto */
        $this->Text( $x+ 111 , $y+(132*$inTamY) , $this->stValorImposto);

        /* tipo */
        $this->Text( $x+ 5 , $y+(139.5*$inTamY) , $this->stTipoUnidade );
        /* zona */
        $this->Text( $x+ 23 , $y+(139.5*$inTamY) , $this->stZona );
        /* area */
        $this->Text( $x+ 42 , $y+(139.5*$inTamY) , $this->stAreaM2 );
        /* balor area*/
        $this->Text( $x+ 58 , $y+(139.5*$inTamY) , $this->stValorM2);
        /* taxa */
        $this->Text( $x+ 75 , $y+(139.5*$inTamY) , $this->stValorTaxa );
        /* ilu*/
        $this->Text( $x+ 108 , $y+(139*$inTamY) , $this->stContribIlumPublica );

        /* total */
        $this->Text( $x+ 110 , $y+(145.5*$inTamY) , $this->stValorTotalTributos );

    }
//	function novaPagina() {
//		$this->addPage();
//	}
//
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
}
}
?>
