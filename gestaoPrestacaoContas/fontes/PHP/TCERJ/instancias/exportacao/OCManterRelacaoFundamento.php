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
    * Página Oculta de Parâmetros do Arquivo de Relacionamento de Recursos
    * Data de Criação   : 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.12
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:12  hboaventura
Ticket#10234#

Revision 1.6  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GPC_TCERJ_NEGOCIO."RExportacaoTCERJRelFundamento.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RExportacaoTCERJRelFundamento();
$obRegra->obRExportacaoTCERJFundamento->listarFundamentos    ( $rsFundamentos ) ;

// Acoes por pagina
switch ($stCtrl) {

    // Monta a lista com os Fundamentos
    case "MontaListaRelacionamentoFundamentos":

          if ($rsFundamentos->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              $obLista->setTitulo( "Dados Para o Relacionamento" );

              $obLista->setRecordSet( $rsFundamentos );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 5 );
              $obLista->commitCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Tipo Norma" );
              $obLista->ultimoCabecalho->setWidth( 80 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Fundamento ExportacaoTCE" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();
              $obLista->addCabecalho();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[cod_tipo_norma] - [nom_tipo_norma]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              /* TextBox Recurso ExportacaoTCE */
              $obTxtFundamentoExportacaoTCE = new TextBox;
              $obTxtFundamentoExportacaoTCE->setRotulo      ( "inCodigoFundamentoLegal"                      );
              $obTxtFundamentoExportacaoTCE->setName        ( "inCodigoFundamentoLegal_[cod_tipo_norma]_"    );
              $obTxtFundamentoExportacaoTCE->setSize        ( 14                                             );
              $obTxtFundamentoExportacaoTCE->setMaxLength   ( 8                                              );
              $obTxtFundamentoExportacaoTCE->setInteiro     ( true                                           );
              $obTxtFundamentoExportacaoTCE->setValue       ( "cod_fundamento_legal"                         );

              $obLista->addDadoComponente   ( $obTxtFundamentoExportacaoTCE           );
              $obLista->ultimoDado->setCampo( "inCodigoFundamentoLegal_"    );
              $obLista->commitDadoComponente(                               );

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista com innerHTML
          $stJs .= "\td.getElementById('spnRelacionamentoFundamentos').innerHTML = '".$stHtml."';\r\n";

    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();
?>
