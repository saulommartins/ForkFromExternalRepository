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
    * Página de Lista de Manter PPA
    * Data de Criação: 21/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * Casos de uso: uc-02.09.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_VISAO.'/VPPAManterPPA.class.php';
require_once CAM_GF_PPA_VISAO.'/VPPAHomologarPPA.class.php';
require_once CAM_GF_PPA_NEGOCIO.'/RPPAManterPPA.class.php';
require_once CAM_GF_PPA_NEGOCIO.'/RPPAHomologarPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma 	= 'ManterPPA';
$pgOcul   	    = 'OC'.$stPrograma.".php";
$pgProc    		= 'PR'.$stPrograma.".php";
$pgFilt 		= 'FL'.$stPrograma.".php";
$pgList    		= 'LS'.$stPrograma.".php";
$pgJs    		= 'JS'.$stPrograma.".php";

include_once $pgJs;

$stAcao = $request->get('stAcao');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName     ('stAcao');
$obHdnAcao->setValue    ($stAcao);

$obController = new RPPAHomologarPPA;
$obVisao      = new VPPAHomologarPPA($obController);
$rsPPA        = $obVisao->pesquisaPPANorma();

$obLista = new Lista;
$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Dados para Exclusão do PPA');

$obLista->setRecordSet($rsPPA);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(3);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Período');
$obLista->ultimoCabecalho->setWidth(100);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('periodo');
$obLista->commitDado();

$stCaminho = CAM_GF_PPA_INSTANCIAS . 'configuracao/PRManterPPA.php?' . Sessao::getId() . '&stAcao=excluir';

$obLista->addAcao();
$obLista->ultimaAcao->setAcao('excluir');
$obLista->ultimaAcao->addCampo('&inCodPPA', 'cod_ppa');
$obLista->ultimaAcao->addCampo('stPeriodo', 'periodo');
$obLista->ultimaAcao->addCampo( "&stDescQuestao","periodo" );
$obLista->ultimaAcao->setLink($stCaminho);
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
