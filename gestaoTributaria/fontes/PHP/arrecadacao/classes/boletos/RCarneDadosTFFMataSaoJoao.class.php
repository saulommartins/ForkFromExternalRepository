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
    * Classe Carne de dados do TFF Mata de Sao Joao
    * Data de Criação: 12/12/2006

    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Componentes

    * $Id: RCarneDadosTFFMataSaoJoao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.11
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once 'RCarneDiversosPetropolis.class.php';
define       ('FPDF_FONTPATH','/var/www/sw1/gestaoAdministrativa/fontes/PHP/FPDF/fonts/');

/*
$Log$
Revision 1.3  2007/03/29 19:32:05  dibueno
Correção de acento na palavra "COMPOSIÇÃO"

Revision 1.2  2007/01/19 18:23:11  cercato
adicionando na capa TFF a inscricao imobiliaria.

Revision 1.1  2006/12/21 17:04:01  cercato
*** empty log message ***

*/

class RCarneDadosTFFMataSaoJoao extends RCarneDiversosPetropolis
{
    /**
     * @access public
     * @var String
     */
    public $stComposicaoCalculo ;
    public $stNomePrefeitura ;
    public $stSubTitulo ;
    public $stExercicio ;
    public $stCodigoLogradouro ;
    public $stNomeLogradouro ;
    public $stComplemento ;
    public $stQuadra ;
    public $stLote ;
    public $stDistrito ;
    public $stRegiao ;
    public $stCep ;
    public $stCidade ;
    public $stEstado ;
    public $stCamLogo;
    public $stInscricaoEconomica;
    public $stInscricaoMunicipal;
    public $stRazaoSocial;
    public $stNomeFantasia;
    public $stCNPJ;
    public $stAtividade;
    public $stResponsavel;
    public $stParcelaUnica;
    public $stDescontoParcelaUnica;
    public $stParcelaUm;
    public $stParcelaDois;
    public $stParcelaTres;

    public function RCarneDadosTFFMataSaoJoao()
    {
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
        $this->stCamLogo = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/logoCarne.png';
    }
    public function desenhaCarne($x , $y)
    {
$stComposição =
"COMPOSIÇÃO DO CÁLCULO\n\n
Lei 280/2006, Art. 148 - A Taxa de Fiscalização do Funcionamento - TFF - dos estabelecimentos em geral tem como fato gerador a sua fiscalização quanto as normas constantes desta Lei, do Código de Polícia Administrativa e do Plano Diretor Urbano, relativas a higiene, poluição do meio ambiente, costumes, ordem, tranqüilidade e segurança pública e será calculada de acordo com a Tabela IV, anexa a esta Lei.\n
A Tabela IV da Lei 280/2006 traz os valores da Taxa de Fiscalização do Funcionamento dos estabelecimentos, de acordo com a atividade exercida.\n
Decreto 332/2006, Art. 6º. A Taxa de Fiscalização do Funcionamento terá vencimento no dia 31 de janeiro de cada exercício.\n
§ 1º Será realizado desconto de 5% (cinco por cento) do valor da Taxa, quando o contribuinte ou responsável efetuar o recolhimento integralmente no prazo estabelecido no caput deste artigo, em quota única.\n
§ 2º O contribuinte que não efetuar o recolhimento da taxa em quota única poderá fazê-lo em até 03 (três) parcelas mensais, na forma e prazos estabelecidos no carnê encaminhado pelo Poder Executivo, fixado o vencimento da primeira parcela em 31 de janeiro.\n
§ 3ºQuando ocorrer o lançamento no curso do exercício, a Taxa de Fiscalização do Funcionamento será calculada proporcionalmente ao número de meses restantes, e o seu recolhimento será efetuado no prazo de 05 (cinco) dias após deferimento da solicitação do lançamento.\n
§ 4ºA expedição do alvará definitivo dar-se-á apenas mediante total quitação da respectiva taxa.\n
a) Caso o contribuinte ou responsável pela taxa opte pelo pagamento parcelado, será expedida uma licença temporária com validade até o vencimento da parcela subseqüente, devendo o contribuinte ou responsável apresentar o pagamento da(s) parcela(s) no órgão responsável pela emissão do alvará, para solicitar a expedição de nova licença temporária, até que, mediante quitação total da taxa, possa receber o alvará
definitivo.\n
Necessitando alterar os dados cadastrais compareça, munido de documentos comprobatórios, ao Setor de Tributos e Fiscalização, órgão da Secretaria Municipal de Administração e Finanças, sito na Rua Santos Dummont, nº 86 - Centro - CEP: 48.280-000 - Mata de São João/Ba .\n";

        $inTamY = 1.15;

//		$this->SetXY($x+10,$y+10);
        $this->stComposicaoCalculo = $stComposição;
        $this->SetXY($x+10,$y+4);
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
        $this->Write( 2.1 , $this->stComposicaoCalculo );
        $this->setLeftMargin(0);

        /**
         * Montar Estrutura dos Dados Cadastrais
         */

        /* Linhas Horizontais */
        $this->Line( $x , $y +  (98*$inTamY) , $x +189, $y +  (98*$inTamY) );

        $this->Line( $x , $y +  (103*$inTamY) , $x +189, $y +  (103*$inTamY) );

        $this->Line( $x , $y + (112*$inTamY) , $x +189, $y + (112*$inTamY) );
        $this->Line( $x , $y + (117*$inTamY) , $x +189, $y + (117*$inTamY) );
        $this->Line( $x , $y + (123*$inTamY) , $x +189, $y + (123*$inTamY) );

        $this->Line( $x , $y + (131*$inTamY) , $x +189, $y + (131*$inTamY) );
        $this->Line( $x , $y + (135*$inTamY) , $x +189, $y + (135*$inTamY) );
        $this->Line( $x , $y + (139*$inTamY) , $x +189, $y + (139*$inTamY) );

        $this->Line( $x , $y + (143*$inTamY) , $x +189, $y + (143*$inTamY) ); //linha acima da ultima frase

        /* Linhas Verticais */
        /* Linha 1*/
        $this->Line( $x + 88 , $y +  (78*$inTamY) , $x + 88 , $y  +  (90*$inTamY));

        /* Linha 2*/
        $this->Line( $x +  44 , $y +  (90*$inTamY) , $x +  44 , $y  +  (103*$inTamY));
        $this->Line( $x + 136 , $y +  (90*$inTamY) , $x + 136 , $y  +  (98*$inTamY));

        /* Linha 3*/
        /* Linha 4*/
        $this->Line( $x +  34 , $y + (112*$inTamY) , $x + 34 , $y  + (117*$inTamY));
        $this->Line( $x + 64 , $y + (112*$inTamY) , $x + 64 , $y  + (117*$inTamY));
        $this->Line( $x + 105 , $y + (112*$inTamY) , $x + 105 , $y  + (117*$inTamY));

        /* Linha 5*/
        $this->Line( $x +  25  , $y + (117*$inTamY) , $x +  25 , $y  + (123*$inTamY));
        $this->Line( $x +  44  , $y + (117*$inTamY) , $x +  44 , $y  + (123*$inTamY));
        $this->Line( $x +  81  , $y + (117*$inTamY) , $x +  81 , $y  + (123*$inTamY));
        $this->Line( $x + 105  , $y + (117*$inTamY) , $x + 105 , $y  + (123*$inTamY));
        $this->Line( $x + 127  , $y + (117*$inTamY) , $x + 127 , $y  + (123*$inTamY));
        $this->Line( $x + 163  , $y + (117*$inTamY) , $x + 163 , $y  + (123*$inTamY));

        $this->Line( $x + 85  , $y + (126*$inTamY) , $x + 85 , $y  + (143*$inTamY));

        /**
         * Titulos dos Dados
         */
        /* imagem*/
        $stExt = substr( $this->stCamLogo, strlen($this->stCamLogo)-3, strlen($this->stCamLogo) );
        $this->Image( $this->stCamLogo , $x+1 , $y+(79*$inTamY) , 10 , 10 , $stExt );
        /* dados */
        $this->setFont( 'Arial','',5 );

        $this->Text( $x+  1 , $y+(95*$inTamY) , 'INSCRIÇÃO ECONÔMICA:' );
        $this->Text( $x+ 45 , $y+(95*$inTamY) , 'RAZÃO SOCIAL:' );
        $this->Text( $x+137 , $y+(95*$inTamY) , 'NOME FANTASIA:' );

        $this->Text( $x+  1 , $y+(100*$inTamY) , 'CNPJ/CPF:' );
        $this->Text( $x+ 45 , $y+(100*$inTamY) , 'ATIVIDADE:' );

        $this->Text( $x+  1 , $y+(105*$inTamY) , 'RESPONSÁVEL:' );

        /* exercicio */
        $this->setFont( 'Arial','B',8 );
        $this->Text( $x+122 , $y+(81*$inTamY) , 'DADOS CADASTRAIS' );
        $this->Text( $x+116 , $y+(84*$inTamY) , '- Cadastro Geral de Atividades -' );

        $this->setFont( 'Arial','',10 );
        $this->Text( $x+122 , $y+(88*$inTamY) , 'EXERCÍCIO  ' . $this->stExercicio );

//tudo q for cga eh cadastro economico

        $this->setFont( 'Arial','',8 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(89*$inTamY) , 189 , 4*$inTamY , 'DF');
        $this->Text( $x+  52 , $y+(92*$inTamY) , 'D A D O S   D O   C A D A S T R O    E C O N Ô M I C O' );

        $this->Rect( $x, $y+(108*$inTamY) , 189 , 4*$inTamY , 'DF');
        $this->Text( $x+  62 , $y+(111*$inTamY) , 'E N D E R E Ç O  D O  L O C A L  E S T A B E L E C I D O' );

        $this->Rect( $x, $y+(123*$inTamY) , 189 , 4*$inTamY , 'DF');
        $this->Text( $x+  72 , $y+(126*$inTamY) , 'D A D O S  D O  L A N Ç A M E N T O' );

        $this->setFont( 'Arial','',5 );
        $this->Text( $x+    1 , $y+(114*$inTamY) , 'INSCRIÇÃO IMOBILIÁRIA' );
        $this->Text( $x+    1 , $y+(116*$inTamY) , 'MUNICIPAL:' );

        $this->Text( $x+   35 , $y+(114*$inTamY) , 'CÓDIGO DO LOGRADOURO:' );
        $this->Text( $x+   65 , $y+(114*$inTamY) , 'NOME DO LOGRADOURO:' );
        $this->Text( $x+  106 , $y+(114*$inTamY) , 'COMPLEMENTO:' );

        $this->Text( $x+    1 , $y+(119*$inTamY) , 'QUADRA:' );
        $this->Text( $x+   26 , $y+(119*$inTamY) , 'LOTE:' );
        $this->Text( $x+   45 , $y+(119*$inTamY) , 'DISTRITO:' );
        $this->Text( $x+   82 , $y+(119*$inTamY) , 'REGIÃO:' );
        $this->Text( $x+  106 , $y+(119*$inTamY) , 'CEP:' );
        $this->Text( $x+  128 , $y+(119*$inTamY) , 'CIDADE:' );
        $this->Text( $x+  164 , $y+(119*$inTamY) , 'ESTADO:' );

        $this->Text( $x+   22 , $y+(130*$inTamY) , 'Parcela única:' );
        $this->Text( $x+   122 , $y+(130*$inTamY) , 'DEMONSTRATIVO PARA PAGAMENTO PARCELADO' );
        $this->Text( $x+   132 , $y+(133*$inTamY) , '1º Parcela:' );
        $this->Text( $x+   132 , $y+(137*$inTamY) , '2º Parcela:' );
        $this->Text( $x+   132 , $y+(142*$inTamY) , '3º Parcela:' );
        $this->Text( $x+   25 , $y+(138*$inTamY) , 'DESCONTO NA COTA UNICA(%):' );

        $this->setFont( 'Arial','',6 );
        $this->Text( $x+3 , $y+(146*$inTamY) , 'Quaisquer dúvidas, consulte o Setor de Tributos e Fiscalização,
na Rua Santos Dummont, 86, Centro, CEP: 48280-000, Tel/Fax: (71) 3635-1669 / (71) 3635-3077.' );
        /* mostrar dados */

        $this->setFont( 'Arial','B',7 );
        $this->Text( $x+14 , $y+(85*$inTamY) , $this->stNomePrefeitura );
        $this->setFont( 'Arial','',6 );
        $this->Text( $x+14 , $y+(88*$inTamY) , $this->stSubTitulo );

        $this->setFont( 'Arial','',5 );
        /* inscricao economica */
        $this->Text( $x+ 2 , $y+(97*$inTamY) , $this->stInscricaoEconomica );

        /* razao social */
        $this->Text( $x+45 , $y+(97*$inTamY) , $this->stRazaoSocial );

        /* nome fantasia */
        $this->Text( $x+137 , $y+(97*$inTamY) , $this->stNomeFantasia );

        /* cnpj/cpf */
        $this->Text( $x+1 , $y+(102*$inTamY) , $this->stCNPJ );

        /* atividade */
        $this->Text( $x+45 , $y+(102*$inTamY) , $this->stAtividade );

        /* responsavel */
        $this->Text( $x+1 , $y+(107*$inTamY) , $this->stResponsavel );

        /* inscricao municipal */
        $this->Text( $x+14 , $y+(116*$inTamY) , $this->stInscricaoMunicipal );

        /* logradouro */
        $this->Text( $x+35 , $y+(116*$inTamY) , $this->stCodigoLogradouro );
        /* nome logradouro */
        $this->Text( $x+65 , $y+(116*$inTamY) , $this->stNomeLogradouro );
        /* complemento */
        $this->Text( $x+107 , $y+(116*$inTamY) , $this->stComplemento );

        /* quadra*/
        $this->Text( $x+ 14 , $y+(122*$inTamY) , $this->stQuadra );
        /* lote */
        $this->Text( $x+ 35 , $y+(122*$inTamY) , $this->stLote);
        /* distrito */
        $this->Text( $x+ 45 , $y+(122*$inTamY) , $this->stDistrito );
        /* regiao */
        $this->Text( $x+ 89 , $y+(122*$inTamY) , $this->stRegiao);
        /* cep */
        $this->Text( $x+108 , $y+(122*$inTamY) , $this->stCep);
        /* cidade */
        $this->Text( $x+129 , $y+(122*$inTamY) , $this->stCidade );
        /* estado */
        $this->Text( $x+170 , $y+(122*$inTamY) , $this->stEstado );

        /* Parcela Unica */
        $this->Text( $x+35 , $y+(130*$inTamY) , $this->stParcelaUnica );

        /* Desconto Parcela Unica */
        $this->Text( $x+55 , $y+(138*$inTamY) , $this->stDescontoParcelaUnica );

        /* Parcela 1 */
        $this->Text( $x+142 , $y+(133*$inTamY) , $this->stParcelaUm );

        /* Parcela 2 */
        $this->Text( $x+142 , $y+(137*$inTamY) , $this->stParcelaDois );

        /* Parcela 3 */
        $this->Text( $x+142 , $y+(142*$inTamY) , $this->stParcelaTres );
    }

    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}
?>
