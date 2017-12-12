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
    * Página de Processamento para Configuração do Organograma
    * Data de criação : 10/12/2008

    * @author Analista: Gelson Wolowski
    * @author Programador: Diogo Zarpelon

    * @ignore

    $Id:$

    **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ORGAN_MAPEAMENTO.'TOrganogramaOrganograma.class.php' );
include_once( CAM_GA_ORGAN_NEGOCIO.'ROrganogramaOrganograma.class.php' );

Sessao::setTrataExcecao( true );

$obRegraOrganograma        = new ROrganogramaOrganograma;
$obTOrganogramaOrganograma = new TOrganogramaOrganograma;

if ($_REQUEST['inCodOrganograma'] != "") {

    # Filtro para listar somente organogramas com data menor ou igual ao dia.
    $stFiltro = " WHERE implantacao <= '".date('Y-m-d')."'";

    $obRegraOrganograma->listarOrganogramas($rsOrganograma, '', '', $stFiltro);

    while (!$rsOrganograma->eof()) {
        # Se o organograma for igual ao escolhido no formulário, seta como ativo = true.
        $boAtivo = ($rsOrganograma->getCampo('cod_organograma') == $_REQUEST['inCodOrganograma']) ? 'true' : 'false';

        $obTOrganogramaOrganograma->setDado('cod_organograma' , $rsOrganograma->getCampo('cod_organograma'));
        $obTOrganogramaOrganograma->setDado('cod_norma'       , $rsOrganograma->getCampo('cod_norma'));
        $obTOrganogramaOrganograma->setDado('implantacao'     , $rsOrganograma->getCampo('implantacao'));
        $obTOrganogramaOrganograma->setDado('ativo'           , $boAtivo);
        $obErro = $obTOrganogramaOrganograma->alteracao();

        $rsOrganograma->proximo();
    }

    if (!$obErro->ocorreu()) {
        SistemaLegado::exibeAviso('Organograma '.$_REQUEST['inCodOrganograma'].' ativado com sucesso.', 'aviso', '');
    }
}

Sessao::encerraExcecao();
