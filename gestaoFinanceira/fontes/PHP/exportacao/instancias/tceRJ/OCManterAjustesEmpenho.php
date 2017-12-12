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
    * Página Oculta de Parâmetros para ajustes do arquivo Empenho.txt
    * Data de Criação   : 15/05/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.14
*/

/*
$Log$
Revision 1.2  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EXP_NEGOCIO."RExportacaoTCERJAjustesEmpenho.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RExportacaoTCERJAjustesEmpenho();

if ($sessao->transf4['inCodEntidade'] != "") {
        foreach ($sessao->transf4['inCodEntidade'] as $key => $valor) {
            $stEntidades .= $valor." , ";
        }
        $stEntidades = substr( $stEntidades, 0, strlen($stEntidades) - 2 );
    }

$obRegra->setExercicio           ( Sessao::getExercicio()                    );
$obRegra->setEntidades           ( $stEntidades                          );
$obRegra->setDataInicial         ( $sessao->transf4['stDataInicial']     );
$obRegra->setDataFinal           ( $sessao->transf4['stDataFinal']       );
$obRegra->listarAjustesEmpenho   ( $rsRecordSet       );

// Acoes por pagina
switch ($stCtrl) {

    // Monta a lista com os Recursos
    case "MontaListaAjustesEmpenho":

          if ($rsRecordSet->getNumLinhas() != 0) {

              $obLista = new Lista;
              $obLista->setMostraPaginacao( false );
              $obLista->setTitulo( "Dados para alteração dos Processos Licitatórios" );

              $obLista->setRecordSet( $rsRecordSet );
              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo("&nbsp;");
              $obLista->ultimoCabecalho->setWidth( 5 );
              $obLista->commitCabecalho();

              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Entidade" );
              $obLista->ultimoCabecalho->setWidth( 55 );
              $obLista->commitCabecalho();

              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Empenho" );
              $obLista->ultimoCabecalho->setWidth( 10 );
              $obLista->commitCabecalho();

              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Modalidade" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();

              $obLista->addCabecalho();
              $obLista->ultimoCabecalho->addConteudo( "Nro Proc. Licitatorio" );
              $obLista->ultimoCabecalho->setWidth( 15 );
              $obLista->commitCabecalho();

              $obLista->addCabecalho();
              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[entidade] - [nom_cgm]" );
              $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
              $obLista->commitDado();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[cod_empenho]" );
              $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
              $obLista->commitDado();

              $obLista->addDado();
              $obLista->ultimoDado->setCampo( "[modalidade]" );
              $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
              $obLista->commitDado();

              $obTxtNroProcLicit = new TextBox;
              $obTxtNroProcLicit->setRotulo      ( "stNroProcLicit"    );
              $obTxtNroProcLicit->setName        ( "stNPL_[entidade]_[cod_empenho]_[cod_pre_empenho]_[cod_atributo]_[nro_proc_licitatorio]_"    );
              $obTxtNroProcLicit->setSize        ( 20                    );
              $obTxtNroProcLicit->setMaxLength   ( 36                    );
              $obTxtNroProcLicit->setValue       ( "[nro_proc_licitatorio]" );

              $obLista->addDadoComponente   ( $obTxtNroProcLicit               );
              $obLista->ultimoDado->setCampo( "[nro_proc_licitatorio]"         );
              $obLista->ultimoDado->setAlinhamento ( 'CENTRO'                  );
              $obLista->commitDadoComponente();

              // Hidden pra levar o Timestamp ao PR
              $obHdnTimestamp = new Hidden;
              $obHdnTimestamp->setName          ( "timestamp"       );
              $obHdnTimestamp->setValue         ( "[timestamp]"     );

              $obLista->addDadoComponente       ( $obHdnTimestamp   );
              $obLista->commitDadoComponente();

              $obLista->montaHTML();
              $stHtml = $obLista->getHTML();
              $stHtml = str_replace("\n","",$stHtml);
              $stHtml = str_replace("  ","",$stHtml);
              $stHtml = str_replace("'","\\'",$stHtml);
          }
          // preenche a lista com innerHTML
          $stJs .= "\td.getElementById('spnAjustesEmpenho').innerHTML = '".$stHtml."';\r\n";

    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs."LiberaFrames(true,false);");
?>
