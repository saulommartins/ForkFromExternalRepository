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
    * Página Oculta de Parâmetros do Arquivo de Relacionamento de Tipo de Alteracao
    * Data de Criação   : 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.13
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCERJRelTipoAlteracao.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RExportacaoTCERJRelTipoAlteracao();
$obRegra->obRExportacaoTCERJTipoAlteracao->setExercicio           ( Sessao::getExercicio() );
$obRegra->obRExportacaoTCERJTipoAlteracao->listarTipoAlteracao    ( $rsTipoAlteracao   );

// Acoes por pagina
switch ($stCtrl) {

    // Monta a lista com os Recursos
    case "MontaListaRelacionamentoTipoAlteracao":

          if ($rsTipoAlteracao->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              $obLista->setTitulo( "Dados Para o Relacionamento" );

              $obLista->setRecordSet( $rsTipoAlteracao );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 5 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Tipo Transferência" );
              $obLista->ultimoCabecalho->setWidth( 80 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Tipo Alteração ExportacaoTCE" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[cod_tipo] - [nom_tipo]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              /* TextBox Recurso ExportacaoTCE */
              $obTxtTipoAlteracaoExportacaoTCE = new TextBox;
              $obTxtTipoAlteracaoExportacaoTCE->setRotulo      ( "inCodigoTipoAlteracao"    );
              $obTxtTipoAlteracaoExportacaoTCE->setName        ( "inCodigoTipoAlteracao_[exercicio]_[cod_tipo]_[tipo]_"    );
              $obTxtTipoAlteracaoExportacaoTCE->setSize        ( 14                    );
              $obTxtTipoAlteracaoExportacaoTCE->setMaxLength   ( 8                    );
              $obTxtTipoAlteracaoExportacaoTCE->setInteiro     ( true                 );
              $obTxtTipoAlteracaoExportacaoTCE->setValue       ( "cod_tipo_alteracao" );

              $obLista->addDadoComponente   ( $obTxtTipoAlteracaoExportacaoTCE          );
              $obLista->ultimoDado->setCampo( "inCodigoTipoAlteracao_"         );
              $obLista->commitDadoComponente();

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista com innerHTML
          $stJs .= "\td.getElementById('spnRelacionamentoTipoAlteracao').innerHTML = '".$stHtml."';\r\n";

    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();
?>
