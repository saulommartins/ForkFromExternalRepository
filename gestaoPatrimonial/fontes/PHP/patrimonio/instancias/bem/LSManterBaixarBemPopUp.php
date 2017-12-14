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
  * Data de Criação: 04/03/2016

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Arthur Cruz

  * @package URBEM
  * @subpackage

  $Id: $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemBaixado.class.php";

$stPrograma = "ManterBaixarBem";
$pgFilt		= "FL".$stPrograma.".php";
$pgList		= "LS".$stPrograma.".php";
$pgForm		= "FM".$stPrograma.".php";
$pgProc		= "PR".$stPrograma.".php";
$pgOcul		= "OC".$stPrograma.".php";
$pgJs		= "JS".$stPrograma.".js";

$stDataBaixa = $request->get('stDataBaixa');
$stMotivo    = $request->get('stMotivo');

if ( isset($stDataBaixa) && isset($stMotivo) ) {
    $stFiltro = " WHERE bem_baixado.dt_baixa = TO_DATE('".$request->get('stDataBaixa')."','dd/mm/yyyy')
                    AND bem_baixado.motivo LIKE '".$request->get('stMotivo')."%'";
    $stLink = "&stDataBaixa=".$request->get('stDataBaixa')."&stMotivo=".$request->get('stMotivo');
}

$stOrder = " \n ORDER BY bem_baixado.cod_bem ";

$obTPatrimonioBemBaixado = new TPatrimonioBemBaixado();
$obTPatrimonioBemBaixado->recuperaBemBaixadoGeral( $rsBem, $stFiltro, $stOrder);

$obLista = new Lista;
$obLista->setTitulo("Histórico Atual do Bem no Patrimônio");
$obLista->setMostraPaginacao(true);
$obLista->obPaginacao->setFiltro("&stLink=".$stLink);
$obLista->setRecordSet($rsBem);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor da Baixa" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_bem" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor_atualizado" );
$obLista->commitDado();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>