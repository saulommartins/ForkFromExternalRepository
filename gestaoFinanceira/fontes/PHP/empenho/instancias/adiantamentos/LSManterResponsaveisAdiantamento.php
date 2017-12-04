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
/*
    * Página de Formulário de responsáveis por adiantamento
    * Data de Criação   : 13/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso : uc-02.03.32
*/

/*
$Log$
Revision 1.3  2007/09/06 20:14:20  luciano
Ticket#9094#

Revision 1.2  2007/08/27 20:39:00  luciano
Bug#10007#

Revision 1.1  2007/08/10 13:58:04  luciano
movido de lugar

Revision 1.9  2007/07/23 15:19:14  luciano
Bug#9366#

Revision 1.8  2007/07/09 20:59:47  luciano
Bug#9366#

Revision 1.7  2007/07/05 16:17:07  luciano
Bug#9366#,Bug#9368#

Revision 1.6  2007/06/29 15:53:40  luciano
Bug#9379#

Revision 1.5  2007/06/25 19:07:54  luciano
Bug#9402#,Bug#9359#,Bug#9094#

Revision 1.4  2007/05/03 20:27:26  luciano
Bug#9094#

Revision 1.3  2007/03/07 15:37:40  luciano
Bug#8610#

Revision 1.2  2007/03/06 14:46:51  gelson
correção do caso de uso.

Revision 1.1  2006/10/18 18:58:11  rodrigo
Caso de Uso 02.03.32

*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include mapeamentos
include_once( TEMP."TEmpenhoContraPartidaResponsavel.class.php"                                     );
include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php"                                      );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stPrograma = "ManterResponsaveisAdiantamento";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

switch ($_REQUEST['stAcao']) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;
}

$arFiltro = Sessao::read('filtro');
// Seta os filtros
$_REQUEST['inCodigoResponsavel' ] = ( $arFiltro['inCodigoResponsavel' ] != "") ? $arFiltro['inCodigoResponsavel' ] : $_REQUEST['inCodigoResponsavel' ] ;
$_REQUEST['inCodigoContaLancamento'] = ( $arFiltro['inCodigoContaLancamento'] != "") ? $arFiltro['inCodigoContaLancamento'] : $_REQUEST['inCodigoContaLancamento'] ;
$_REQUEST['inCodigoContraPartida'  ] = ( $arFiltro['inCodigoContraPartida'  ] != "") ? $arFiltro['inCodigoContraPartida'  ] : $_REQUEST['inCodigoContraPartida'  ] ;

$rsResponsaveis                     = new RecordSet();
$obTEmpenhoResponsavelAdiantamento  = new TEmpenhoResponsavelAdiantamento;

   $stFiltro = " AND responsavel_adiantamento.exercicio = '".Sessao::getExercicio()."'                    \n";

if ($_REQUEST['inCodigoResponsavel' ] !="") {
   $stFiltro.= " AND responsavel_adiantamento.numcgm = ".$_REQUEST['inCodigoResponsavel']."                \n";
   $arFiltro['inCodigoResponsavel' ] = $_REQUEST['inCodigoResponsavel'];
}

if ($_REQUEST['inCodigoContaLancamento'] !="") {
   $stFiltro.= " AND responsavel_adiantamento.conta_lancamento    = ".$_REQUEST['inCodigoContaLancamento']."\n";
   $arFiltro['inCodigoContaLancamento' ] = $_REQUEST['inCodigoContaLancamento'];
}

if ($_REQUEST['inCodigoContraPartida'  ] !="") {
   $stFiltro.= " AND responsavel_adiantamento.conta_contrapartida = ".$_REQUEST['inCodigoContraPartida']."  \n";
   $arFiltro['inCodigoContraPartida' ] = $_REQUEST['inCodigoContraPartida'];
}

$obTEmpenhoResponsavelAdiantamento->recuperaResponsavelAdiantamento( $rsResponsaveis, $stFiltro );

Sessao::write('filtro', $arFiltro);
if ($arFiltro) {
    $stFiltroLink = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltroLink .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltroLink .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stLink.="&stAcao=".$stAcao.$stFiltroLink;

$obLista = new Lista;
$obLista->setRecordSet( $rsResponsaveis );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Contrapartida Contábil");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta Contábil");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "conta_contrapartida" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "conta_lancamento" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodContraPartida","conta_contrapartida");

if ( ($stAcao == 'alterar') ) {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
} elseif ( ($stAcao == 'excluir')) {
    $stCaminho = CAM_GF_EMP_INSTANCIAS."adiantamentos/".$pgProx;
    $obLista->ultimaAcao->addCampo("&stDescQuestao","numcgm");
    $obLista->ultimaAcao->addCampo("&numcgm","numcgm");
    $obLista->ultimaAcao->setLink( $stCaminho."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();

$obLista->setAjuda("UC-02.03.32");
$obLista->Show();
