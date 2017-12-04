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
    * Página de Formulario de Ajustes do ContaCont.txt com o Elenco de Contas do TCERJ
    * Data de Criação   : 26/07/2006

* @author Analista: Diego Victoria
* @author Desenvolvedor: Anderson C. Konze

$Revision: 30668 $
$Name$
$Author: cako $
$Date: 2006-07-28 11:18:27 -0300 (Sex, 28 Jul 2006) $

Casos de uso: uc-02.08.16
*/

/*
$Log$
Revision 1.1  2006/07/28 14:14:49  cako
Bug #6568#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

switch ($_GET['stCtrl']) {
    case 'montaListaContas':

        include_once( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCERJAjustesContaCont.php");

        $sessao = $_SESSION ['sessao'];
        if ($_GET['stCodEstrutural']) {

            $obTExportacaoAjustesContaCont = new TExportacaoTCERJAjustesContaCont;
            $obTExportacaoAjustesContaCont->setDado("exercicio", Sessao::getExercicio() );
            $obTExportacaoAjustesContaCont->setDado("cod_estrutural", $_GET['stCodEstrutural'] );

            $obErro = $obTExportacaoAjustesContaCont->recuperaDadosAjustesTC( $rsLista );
            if ( !$obErro->ocorreu() ) {

                if ($rsLista->getNumLinhas() != 0) {
                    $obLista = new Lista;
                    $obLista->setMostraPaginacao( false );
                    $obLista->setTitulo( "Dados para inclusão/alteração do Sequencial TC" );

                    $obLista->setRecordSet( $rsLista );
                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
                    $obLista->ultimoCabecalho->setWidth( 3 );
                    $obLista->commitCabecalho();

                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo( "Conta" );
                    $obLista->ultimoCabecalho->setWidth( 13 );
                    $obLista->commitCabecalho();

                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
                    $obLista->ultimoCabecalho->setWidth( 60 );
                    $obLista->commitCabecalho();

                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo( "Sequencial TC" );
                    $obLista->ultimoCabecalho->setWidth( 15 );
                    $obLista->commitCabecalho();

                    $obLista->addCabecalho();
                    $obLista->addDado();
                    $obLista->ultimoDado->setCampo( "[cod_estrutural]" );
                    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
                    $obLista->commitDado();

                    $obLista->addDado();
                    $obLista->ultimoDado->setCampo( "[nom_conta]" );
                    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
                    $obLista->commitDado();

                    $obTxtSequencial = new TextBox;
                    $obTxtSequencial->setRotulo      ( ""    );
                    $obTxtSequencial->setName        ( "tce_[cod_conta]_[cod_sequencial]_");
                    $obTxtSequencial->setSize        ( 2                    );
                    $obTxtSequencial->setMaxLength   ( 4                    );
                    $obTxtSequencial->setInteiro     ( true );
                    $obTxtSequencial->setValue       ( "[cod_sequencial]" );

                    $obLista->addDadoComponente   ( $obTxtSequencial  );
                    $obLista->ultimoDado->setCampo( "[cod_sequencial]"  );
                    $obLista->ultimoDado->setAlinhamento ( 'CENTRO' );
                    $obLista->commitDadoComponente();

                    $obLista->montaHTML();
                    $stHtml = $obLista->getHTML();
                    $stHtml = str_replace("\n"," ",$stHtml);
                    $stHtml = str_replace("\\","\\\\",$stHtml);
                    $stHtml = str_replace("'","\\'",$stHtml);
                    $stHtml = str_replace("\r"," ",$stHtml);

                }
          // preenche a lista com innerHTML
                $stJs .= "\td.getElementById('spnListaContas').innerHTML = '".$stHtml."';\r\n";
                $stJs .= "\td.getElementById('btnOk').style.display = 'block';\r\n";
                $stJs .= "\td.getElementById('btnOk').style.width = '65px';\r\n";
            }
        } else $stJs .= "alertaAviso('É necessário informar uma conta para filtrar.','frm','erro','".Sessao::getId()."'); \n";
        break;

     case "limpaListaContas":
       $stJs .= " d.getElementById('spnListaContas').innerHTML = '';\r\n";
       $stJs .= " d.getElementById('btnOk').style.display = 'none';\r\n";
       $stJs .= " f.stCodEstrutural.value = '';\r\n";
       $stJs .= " f.stCodEstrutural.focus();\r\n";
     break;
}

if ($stJs) {
    echo $stJs;
}
?>
