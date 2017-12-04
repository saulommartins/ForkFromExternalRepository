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
    * Classe Oculta de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2007-08-24 17:21:27 -0300 (Sex, 24 Ago 2007) $

    * Casos de uso: uc-02.03.24
*/

/*
$Log$
Revision 1.5  2007/08/24 20:18:24  vitor
Bug#6005#

Revision 1.4  2006/07/05 20:48:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );

function montaLista($arRecordSet , $boExecuta = true)
{
        $rsRecordSet = new RecordSet();
        $rsRecordSet->preenche( $arRecordSet );

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

        $table = new TableTree();

        $rsRecordSet->addFormatacao( "vl_total", "NUMERIC_BR" );
        $rsRecordSet->addFormatacao( "vl_liquidado", "NUMERIC_BR" );

        $table->setRecordset( $rsRecordSet );
        $table->setArquivo( CAM_GF_EMP_INSTANCIAS . 'liquidacao/OCConsultarLiquidacao.php');
        $table->setParametros( array( "exercicio" , "cod_empenho", "cod_entidade", "cod_nota", "num_item") );
        $table->setComplementoParametros( "stCtrl=detalharLiquidacao");

        // Defina o título da tabela
        $table->setSummary( 'Itens do empenho' );

        $table->addCondicionalTree( 'possui_estornos' , 't' );

        // lista zebrada
        ////$table->setConditional( true , "#efefef" );

        $table->Head->addCabecalho( 'Descrição' , 60  );
        $table->Head->addCabecalho( 'Empenhado' , 10  );
        $table->Head->addCabecalho( 'Liquidado' , 10  );

        $table->Body->addCampo( 'nom_item', 'C' );
        $table->Body->addCampo( 'vl_total', 'D' );
        $table->Body->addCampo( 'vl_liquidado', 'D' );

        $table->montaHTML();

        $stHTML = $table->getHtml();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."'; d.getElementById('nuValorTotal').innerHTML='".$nuVlTotal."';");
          } else {
        return $stHTML;
         }
}

switch ($stCtrl) {
    case 'montaListaItemPreEmpenho':
        montaLista( Sessao::read('arItens') );
    break;

    case 'detalharLiquidacao':

      include_once ( CAM_GF_EMP_NEGOCIO  ."REmpenhoEmpenho.class.php"                    );
      $obREmpenhoEmpenho                    =  new REmpenhoEmpenho;

      $obREmpenhoEmpenho->setExercicioEmissao($_REQUEST['exercicio']);
      $obREmpenhoEmpenho->setCodEmpenho($_REQUEST['cod_empenho']);
      $obREmpenhoEmpenho->setROrcamentoEntidade($_REQUEST['cod_entidade']);
      $obREmpenhoEmpenho->setCodLiquidacao($_REQUEST['cod_nota']);
      $obREmpenhoEmpenho->setNumItemEmpenho($_REQUEST['num_item']);
      $obErro = $obREmpenhoEmpenho->listarLiquidacaoAnuladaPorItem( $rsRecordSet, $boTransacao );

      while ( !$rsRecordSet->eof() ) {
         $inTotalAnulado  += $rsRecordSet->getCampo( 'vl_anulado' );
         $rsRecordSet->proximo();
      }
      $rsRecordSet->setPrimeiroElemento();

      $rsRecordSet->addFormatacao( "vl_total", "NUMERIC_BR" );
      $rsRecordSet->addFormatacao( "vl_anulado", "NUMERIC_BR" );

      $rsLista = new RecordSet;
      $rsLista->preenche( $rsRecordSet );

      $obLista = new Lista;
      $obLista->setMostraPaginacao( false );
      $obLista->setTitulo( 'Anulações de Liquidação' );
      $obLista->setRecordSet( $rsRecordSet );
      $obLista->addCabecalho();
      $obLista->ultimoCabecalho->addConteudo("&nbsp;");
      $obLista->ultimoCabecalho->setWidth( 5 );
      $obLista->commitCabecalho();
      $obLista->addCabecalho();
      $obLista->ultimoCabecalho->addConteudo("Data Liquidação");
      $obLista->ultimoCabecalho->setWidth( 20 );
      $obLista->commitCabecalho();
      $obLista->addCabecalho();
      $obLista->ultimoCabecalho->addConteudo("Valor Item");
      $obLista->ultimoCabecalho->setWidth( 20 );
      $obLista->commitCabecalho();
      $obLista->addCabecalho();
      $obLista->ultimoCabecalho->addConteudo("Data Anulação");
      $obLista->ultimoCabecalho->setWidth( 20 );
      $obLista->commitCabecalho();
      $obLista->addCabecalho();
      $obLista->ultimoCabecalho->addConteudo("Valor Anulado");
      $obLista->ultimoCabecalho->setWidth( 20 );
      $obLista->commitCabecalho();
      $obLista->addDado();
      $obLista->ultimoDado->setCampo( "dt_liquidacao" );
      $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
      $obLista->commitDado();
      $obLista->addDado();
      $obLista->ultimoDado->setCampo( "vl_total" );
      $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
      $obLista->commitDado();
      $obLista->addDado();
      $obLista->ultimoDado->setCampo( "timestamp" );
      $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
      $obLista->commitDado();
      $obLista->addDado();
      $obLista->ultimoDado->setCampo( "vl_anulado" );
      $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
      $obLista->commitDado();
      $obLista->montaHTML();

      $stHTML = $obLista->getHTML();
      $stHTML = str_replace( "\n" ,"" ,$stHTML );
      $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
      $stHTML = str_replace( "  " ,"" ,$stHTML );
      $stHTML = str_replace( "'","\'",$stHTML );
      $stHTML = str_replace( "\\\'","\'",$stHTML );
      echo $stHTML;

      $obFormulario = new Formulario();
      $obLblTotalAnulado = new Label();
      $obLblTotalAnulado->setRotulo( 'Total Anulado'            );
      $obLblTotalAnulado->setValue ( number_format( $inTotalAnulado, 2, ',' , '.' ) );
      $obFormulario->addComponente      ( $obLblTotalAnulado    );
      $obFormulario->montaInnerHTML();
      $stHTML = $obFormulario->getHTML();
      echo $stHTML;

    break;
}
?>
