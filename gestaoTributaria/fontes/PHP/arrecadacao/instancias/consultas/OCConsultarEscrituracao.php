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
  * Página de Consulta de Escrituração
  * Data de criação : 30/12/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Cassiano de Vasconcellos Ferreira

    * $Id: OCConsultarEscrituracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.19
**/

/*
$Log$
Revision 1.2  2007/05/09 20:14:18  cercato
Bug #9238#

Revision 1.1  2007/02/22 12:21:43  cassiano
Consulta escrituração

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_REQUEST["stCtrl"]) {
    case 'listaModalidade':
        include_once(CAM_GT_CEM_MAPEAMENTO.'TCEMAtividadeCadastroEconomico.class.php');
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
        $obTCEMAtividadeCadastroEconomico = new TCEMAtividadeCadastroEconomico();
        $stFiltro = "WHERE cadastro_economico.inscricao_economica = ".$_REQUEST['inscricao_economica'];
        $obTCEMAtividadeCadastroEconomico->recuperaModalidadeLancamento($rsLista,$stFiltro);

        $table = new Table();
        $table->setRecordset( $rsLista );

        //$table->setParametros( array( 'cod_modalidade', 'nom_modalidade') );
        $table->Head->addCabecalho( 'Código' , 20  );
        $table->Head->addCabecalho( 'Atividade' , 40  );
        $table->Head->addCabecalho( 'Modalidade' , 40  );

        $table->Body->addCampo( 'cod_modalidade', 'C' );
        $table->Body->addCampo( 'nom_atividade', 'E' );
        $table->Body->addCampo( 'nom_modalidade', 'E' );
        $table->Body->addAcao( 'consultar' ,  'consultarEscrituracao(%s,%s,%s,%s,%s,%s)' , array( 'cod_modalidade' , 'cod_atividade' , 'inscricao_economica',$_REQUEST['numcgm'],$_REQUEST['timestamp'],$_REQUEST['competencia']));
        $table->montaHTML();
        echo $table->getHtml();
    break;
    case 'listaServico':
        //buscar direto na listade servico(faturamento servico)
        include_once ( CAM_GT_ARR_MAPEAMENTO.'TARRFaturamentoServico.class.php' );
        $obTARRFaturamentoServico = new TARRFaturamentoServico();

        $stFiltro  = " WHERE \n";
        $stFiltro .= "     nota_servico.cod_nota = $_REQUEST[inCodNota]\n";

        $obTARRFaturamentoServico->recuperaRelacionamentoNota($rsListaServico, $stFiltro);
        $rsListaServico->addFormatacao('valor_declarado','NUMERIC_BR_NULL');
        $rsListaServico->addFormatacao('valor_deducao','NUMERIC_BR_NULL');
        $rsListaServico->addFormatacao('valor_retido','NUMERIC_BR_NULL');
        $rsListaServico->addFormatacao('valor_lancado','NUMERIC_BR_NULL');
        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
        $table = new Table();
        $table->setRecordset( $rsListaServico );

        $table->setSummary('Lista de Serviços');

        //$table->setParametros( array( 'cod_modalidade', 'nom_modalidade') );
        $table->Head->addCabecalho( 'Serviço' ,          16 );
        $table->Head->addCabecalho( 'Alíquota(%)',      16 );
        $table->Head->addCabecalho( 'Valor Declarado' , 16 );
        $table->Head->addCabecalho( 'Dedução' ,         16 );
        $table->Head->addCabecalho( 'Valor Retido' ,    16 );
        $table->Head->addCabecalho( 'Valor Lançado' ,   16 );

        $table->Body->addCampo( '[cod_servico] - [nom_servico]' , 'C' );
        $table->Body->addCampo( 'aliquota', 'C' );
        $table->Body->addCampo( 'valor_declarado', 'C' );
        $table->Body->addCampo( 'valor_deducao', 'C' );
        $table->Body->addCampo( 'valor_retido', 'C' );
        $table->Body->addCampo( 'valor_lancado', 'C' );

        $table->Foot->addSoma ( 'valor_lancado', "D" );

        $table->montaHTML();
        $stHTML = $table->getHtml();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        echo "d.getElementById('stListaServicos').innerHTML='".$stHTML."';";
    break;
    case 'consultarDetalheValoresDataBase':

        $stFiltroDetalhe = " ac.cod_calculo = ".$_REQUEST['inCodCalculo'];
        include_once( CAM_GT_ARR_MAPEAMENTO.'TARRCarne.class.php');
        $obTARRCarne = new TARRCarne();
        $arDataBase = explode("/",$_REQUEST[dtDataBase]);
        $dtDataBaixa = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];
        $obTARRCarne->recuperaConsulta($rsDetalhamentoValores,$stFiltroDetalhe,'','',$dtDataBaixa,'','');
        //$obTARRCarne->debug();
        $rsDetalhamentoValores->addFormatacao('valor_total','NUMERIC_BR_NULL');
        $rsDetalhamentoValores->addFormatacao('parcela_multa_pagar','NUMERIC_BR_NULL');
        $rsDetalhamentoValores->addFormatacao('parcela_juros_pagar','NUMERIC_BR_NULL');
        $rsDetalhamentoValores->addFormatacao('parcela_correcao_pagar','NUMERIC_BR_NULL');
        $nuValorPagar = $rsDetalhamentoValores->getCampo('parcela_valor') - $rsDetalhamentoValores->getCampo('parcela_valor_desconto');
        $nuValorPagar = number_format ( $nuValorPagar, 2, ",", ".");

        $stJs .= "document.getElementById('nuValorPagar').innerHTML = '".$nuValorPagar."';\n";
        $stJs .= "document.getElementById('nuParcelaJurosPagar').innerHTML = '".$rsDetalhamentoValores->getCampo('parcela_juros_pagar')."';\n";
        $stJs .= "document.getElementById('nuParcelaMultaPagar').innerHTML = '".$rsDetalhamentoValores->getCampo('parcela_multa_pagar')."';\n";
        $stJs .= "document.getElementById('nuParcelaCorrecaPagar').innerHTML = '".$rsDetalhamentoValores->getCampo('parcela_correcao_pagar')."';\n";
        $stJs .= "document.getElementById('nuTotalPagar').innerHTML = '".$rsDetalhamentoValores->getCampo('valor_total')."';\n";
        echo $stJs;
    break;
}
