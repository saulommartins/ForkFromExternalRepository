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
    * Classe Carne de dados do ISS Mata de Sao Joao
    * Data de Criação: 19/01/2007

    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Componentes

    * $Id: RCarneDadosISSEstimadoMataSaoJoao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.11
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once 'RCarneDiversosPetropolis.class.php';
define       ('FPDF_FONTPATH','/var/www/sw1/gestaoAdministrativa/fontes/PHP/FPDF/fonts/');

/*
$Log$
Revision 1.2  2007/01/23 15:56:26  cercato
aumentando tamanho da letra da capa do iss

Revision 1.1  2007/01/19 11:28:10  cercato
*** empty log message ***

*/

class RCarneDadosISSEstimadoMataSaoJoao extends RCarneDiversosPetropolis
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
    public $stAliquota;
    public $stCidade ;
    public $stEstado ;
    public $stCamLogo;
    public $stInscricaoEconomica;
    public $stNumeroAviso;
    public $stTipoImposto;
    public $stRazaoSocial;
    public $stNomeFantasia;
    public $stCNPJ;
    public $stAtividade;
    public $stResponsavel;
    public $stParcelaUnica;
    public $stParcelaUm;
    public $stParcelaDois;
    public $stParcelaTres;
    public $stParcelaQuatro;
    public $stParcelaCinco;
    public $stParcelaSeis;
    public $stParcelaSete;
    public $stParcelaOito;
    public $stParcelaNove;
    public $stParcelaDez;
    public $stParcelaOnze;
    public $stParcelaDoze;
    public $stBaseCalculo;

    public function RCarneDadosISSEstimadoMataSaoJoao()
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
"\n\n\n                                                                        OBSERVAÇÕES\n\n
  * O pagamento deste carnê não quita débitos anteriores.\n\n
  * As parcelas vencidas estão sujeitas a acréscimos legais de multa e juros de mora descritos na Legislação Municipal em vigor.\n\n
  * O prazo para reclamação do lançamento será de 5 (cinco) dias contados da data de entrega do carnê constante no Protocolo da Prefeitura.\n\n
         - P A G Á V E L   E M   Q U A L Q U E R   A G Ê N C I A   B A N C Á R I A   C O N V E N I A D A -";

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
        $this->setFont( 'Arial', '', 11 );
        $this->setLeftMargin(10);
        $this->Write( 3.5 , $this->stComposicaoCalculo );
        $this->setLeftMargin(0);

        /**
         * Montar Estrutura dos Dados Cadastrais
         */
        /* Linhas Horizontais */
        $this->Line( $x , $y +  (89*$inTamY) , $x +189, $y +  (89*$inTamY) );

        $this->Line( $x , $y +  (97*$inTamY) , $x +104, $y +  (97*$inTamY) );

        $this->Line( $x , $y + (112*$inTamY) , $x +104, $y + (112*$inTamY) );
        $this->Line( $x , $y + (121*$inTamY) , $x +104, $y + (121*$inTamY) );

        $this->Line( $x , $y + (141*$inTamY) , $x +104, $y + (141*$inTamY) );

        /* Linhas Verticais */
        $this->Line( $x + 29 , $y +  (89*$inTamY) , $x + 29 , $y  +  (97*$inTamY));
        $this->Line( $x + 48 , $y +  (89*$inTamY) , $x + 48 , $y  +  (97*$inTamY));
        $this->Line( $x + 70 , $y +  (89*$inTamY) , $x + 70 , $y  +  (97*$inTamY));
        $this->Line( $x + 104 , $y +  (89*$inTamY) , $x + 104 , $y  + (147*$inTamY)); //linha grande central

        $this->Line( $x + 48  , $y + (141*$inTamY) , $x + 48 , $y  + (147*$inTamY)); //ultima linha

        /**
         * Titulos dos Dados
         */
        /* imagem*/
        $stExt = substr( $this->stCamLogo, strlen($this->stCamLogo)-3, strlen($this->stCamLogo) );
        $this->Image( $this->stCamLogo , $x+1 , $y+(79*$inTamY) , 10 , 10 , $stExt );
        /* dados */
        $this->setFont( 'Arial','',5 );

        $this->Text( $x+  1 , $y+(91*$inTamY) , 'INSCRIÇÃO CADASTRAL' );
        $this->Text( $x+ 30 , $y+(91*$inTamY) , 'NÚMERO DE AVISO' );
        $this->Text( $x+ 49 , $y+(91*$inTamY) , 'TIPO DE IMPOSTO' );
        $this->Text( $x+ 71 , $y+(91*$inTamY) , 'EXERCÍCIO' );

        $this->Text( $x+  1 , $y+(100*$inTamY) , 'CONTRIBUINTE' );
        $this->Text( $x+  1 , $y+(114*$inTamY) , 'LOCAL DO ESTABELECIMENTO' );
        $this->Text( $x+  1 , $y+(123*$inTamY) , 'ENDEREÇO DO CONTRIBUINTE' );
        $this->Text( $x+  1 , $y+(143*$inTamY) , 'BASE DE CÁLCULO MENSAL' );
        $this->Text( $x+ 50 , $y+(143*$inTamY) , 'ALÍQUOTA (%)' );

        $this->Text( $x+   121 , $y+(91*$inTamY)  , 'DEMONSTRATIVO DAS PARCELAS' );
        $this->Text( $x+   130 , $y+(95*$inTamY)  , 'Unica' );
        $this->Text( $x+   132 , $y+(99*$inTamY)  , '01' );
        $this->Text( $x+   132 , $y+(103*$inTamY) , '02' );
        $this->Text( $x+   132 , $y+(107*$inTamY) , '03' );
        $this->Text( $x+   132 , $y+(111*$inTamY) , '04' );
        $this->Text( $x+   132 , $y+(115*$inTamY) , '05' );
        $this->Text( $x+   132 , $y+(119*$inTamY) , '06' );
        $this->Text( $x+   132 , $y+(123*$inTamY) , '07' );
        $this->Text( $x+   132 , $y+(127*$inTamY) , '08' );
        $this->Text( $x+   132 , $y+(131*$inTamY) , '09' );
        $this->Text( $x+   132 , $y+(135*$inTamY) , '10' );
        $this->Text( $x+   132 , $y+(139*$inTamY) , '11' );
        $this->Text( $x+   132 , $y+(143*$inTamY) , '12' );

        /* mostrar dados */
        $this->setFont( 'Arial','B',9 );
        $this->Text( $x+44 , $y+(83*$inTamY) , $this->stNomePrefeitura );

        $this->setFont( 'Arial','',7 );
        $this->Text( $x+50 , $y+(88*$inTamY) , $this->stSubTitulo );

        $this->setFont( 'Arial','',5 );
        /* inscricao economica */
        $this->Text( $x+3 , $y+(94*$inTamY) , $this->stInscricaoEconomica );

        /* numero de aviso */
        $this->Text( $x+30 , $y+(94*$inTamY) , $this->stNumeroAviso );

        /* tipo de imposto */
        $this->Text( $x+51 , $y+(94*$inTamY) ,$this->stTipoImposto );

        /* exercicio */
        $this->Text( $x+75 , $y+(94*$inTamY) , $this->stExercicio );

        /* contribuinte */
        $this->Text( $x+1 , $y+(104*$inTamY) , $this->stRazaoSocial );
        $this->Text( $x+1 , $y+(108*$inTamY) , $this->stNomeFantasia );

        /* local do estabelecimento */
        $this->Text( $x+1 , $y+(117*$inTamY) , $this->stNomeLogradouro.' '.$this->stComplemento );

        /* endereco do contribuinte */
        $this->Text( $x+1 , $y+(128*$inTamY) , $this->stNomeLogradouro.' '.$this->stComplemento.' '.$this->stCep );
        $this->Text( $x+1 , $y+(132*$inTamY) , $this->stCidade.' '.$this->stEstado );

        /* base de calculo mensal */
        $this->Text( $x+4 , $y+(146*$inTamY) , $this->stBaseCalculo );

        /* aliquota */
        $this->Text( $x+54 , $y+(146*$inTamY) , $this->stAliquota );

        /* Parcela Unica */
        $this->Text( $x+135 , $y+(95*$inTamY) , $this->stParcelaUnica );

        /* Parcela 1 */
        $this->Text( $x+135 , $y+(99*$inTamY) , $this->stParcelaUm );

        /* Parcela 2 */
        $this->Text( $x+135 , $y+(103*$inTamY) , $this->stParcelaDois );

        /* Parcela 3 */
        $this->Text( $x+135 , $y+(107*$inTamY) , $this->stParcelaTres );

        /* Parcela 4 */
        $this->Text( $x+135 , $y+(111*$inTamY) , $this->stParcelaQuatro );

        /* Parcela 5 */
        $this->Text( $x+135 , $y+(115*$inTamY) , $this->stParcelaCinco );

        /* Parcela 6 */
        $this->Text( $x+135 , $y+(119*$inTamY) , $this->stParcelaSeis );

        /* Parcela 7 */
        $this->Text( $x+135 , $y+(123*$inTamY) , $this->stParcelaSete );

        /* Parcela 8 */
        $this->Text( $x+135 , $y+(127*$inTamY) , $this->stParcelaOito );

        /* Parcela 9 */
        $this->Text( $x+135 , $y+(131*$inTamY) , $this->stParcelaNove );

        /* Parcela 10 */
        $this->Text( $x+135 , $y+(135*$inTamY) , $this->stParcelaDez );

        /* Parcela 11 */
        $this->Text( $x+135 , $y+(139*$inTamY) , $this->stParcelaOnze );

        /* Parcela 12 */
        $this->Text( $x+135 , $y+(143*$inTamY) , $this->stParcelaDoze );
    }

    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}
?>
