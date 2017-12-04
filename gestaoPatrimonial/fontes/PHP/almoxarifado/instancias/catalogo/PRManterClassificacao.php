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
 * Página de Processamento Almoxarifado
 * Data de Criação   : 11/11/2005

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Er Galvão Abbott

 * @ignore

 * Casos de uso: uc-03.03.05

 $Id: PRManterClassificacao.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO. "RAlmoxarifadoCatalogo.class.php";

$stAcao = $request->get("stAcao");

//Define o nome dos arquivos PHP
$stPrograma = "ManterClassificacao";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

$obRegra = new RAlmoxarifadoCatalogoClassificacao;
$inCodAtributosSelecionados = $_REQUEST['inCodAtributosSelecionados'];

switch ($stAcao) {

    case "incluir":

        for ($inCount = 0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }

        $obRegra->obRAlmoxarifadoCatalogo->setCodigo                 ( $_REQUEST['inCodCatalogo'] );
        $obRegra->obRAlmoxarifadoCatalogo->addCatalogoNivel();
        $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel ($_REQUEST['inCodNivel']);
        $descricao = str_replace('\\','',$_REQUEST['stDescricaoNivel']);

        $obRegra->setDescricao ($descricao);

        # Recupera a máscara
        $obRAlmoxarifadoCatalogo = new RAlmoxarifadoCatalogo;
        $obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodCatalogo']);
        $obRAlmoxarifadoCatalogo->consultar();
        $inNroMascara = strlen($obRAlmoxarifadoCatalogo->arCatalogoNivel[0]->getMascara());

        $stCodEstrutural = "";

        foreach ($_REQUEST as $chv => $val) {

            if ($_REQUEST['inCodNivel'] > 1) {
                if (preg_match('/inCodClassificacao_/', $chv)) {

                    # Seta o nível conforme o valor do REQUEST, inCodClassificacao_1, inCodClassificacao_2 e assim por diante
                    $obRegra->setCodNivel($val);

                    $stCodEstrutural .= str_pad($val, $inNroMascara, "0", STR_PAD_LEFT).".";
                }
            } else {
                if (preg_match('/inCodigoClassificacao/', $chv)) {
                    $obRegra->setCodNivel($val);
                }
            }
        }

        $stCodEstrutural = substr($stCodEstrutural, 0, -1);

        # Seta o código estrutural até o nível que deve ser inserido, para buscar corretamente o próximo estrutural livre.
        # ex: Se for inserir no nível 3, busca '001.001' e retorna o próximo livre no nível 3.
        $obRegra->setEstruturalMae($stCodEstrutural);

        if ($stErro == '') {
            $obErro = $obRegra->incluir();

            if (!$obErro->ocorreu()) {
                sistemaLegado::exibeAviso (urlencode($obRegra->getEstrutural() . " - ".$obRegra->getDescricao())   ,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso(urlencode($stErro),"","aviso");
        }

    break;

    case "alterar":

        $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_POST['inCodCatalogo']);
        $obRegra->setCodigo($_POST['inCodigoClassificacao']);

        if ($_REQUEST['hdnDescNivel']) {
            $obRegra->setDescricao($_POST['hdnDescNivel']);
        } else {
            $descricao = str_replace('\\','',$_REQUEST['stDescricaoNivel']);
            $obRegra->setDescricao ($descricao);
        }

        $obRegra->obRAlmoxarifadoCatalogo->addCatalogoNivel();
        $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel ( $_REQUEST['inNivel'] );

        for ($inCount = 0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }

        # Seta TODO o código estrutural para evitar erro na alteração.
        $obRegra->setEstruturalMae(trim($_REQUEST['stCodigoEstrutural']));

        $obErro = $obRegra->alterar();

        if ($obErro->ocorreu()) {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        } else {
            SistemaLegado::alertaAviso($pgList, urlencode($obRegra->getEstrutural()." - ".$obRegra->getDescricao()),"alterar","aviso", Sessao::getId(), "../");
        }

    break;

    case "excluir";

        $obRegra->obRAlmoxarifadoCatalogo->setCodigo($_REQUEST['inCodigo']);
        $obRegra->obRAlmoxarifadoCatalogo->addCatalogoNivel();
        $obRegra->obRAlmoxarifadoCatalogo->roCatalogoNivel->setNivel($_REQUEST['inNivel']);
        $obRegra->setCodigo($_REQUEST['inCodigoClassificacao']);
        $obRegra->consultar();

        $obErro = $obRegra->excluir();

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgList,$obRegra->getEstrutural()." - ".$obRegra->getDescricao(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }

    break;

}

?>
