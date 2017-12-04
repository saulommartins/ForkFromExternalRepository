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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-03.01.06

    $Id: OCIMontaClassificacao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_PAT_COMPONENTES."ISelectEspecie.class.php" );
include_once ( CAM_GP_PAT_COMPONENTES."ISelectGrupo.class.php" );
include_once ( CAM_GP_PAT_COMPONENTES."ISelectNatureza.class.php" );
include_once ( CAM_GP_PAT_COMPONENTES."IMontaClassificacao.class.php" );

$obIMontaClassificacao = Sessao::read('IMontaClassificacao');
$arMascara = explode('.',$obIMontaClassificacao->stMascara);

switch ($_REQUEST['stCtrl']) {
    case 'preencheClassificacao':
        if ($_REQUEST['inCodNatureza'] != '') {
            $stJs = "$('".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').value = '".str_pad($_REQUEST['inCodNatureza'],strlen($arMascara[0]),'0',STR_PAD_LEFT).".';";
        } else {
            $stJs = "$('".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').value = '';";
        }
        if ($_REQUEST['inCodGrupo'] != '') {
            $stJs = "$('".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').value = '".str_pad($_REQUEST['inCodNatureza'],strlen($arMascara[0]),'0',STR_PAD_LEFT).".".str_pad($_REQUEST['inCodGrupo'],strlen($arMascara[1]),'0',STR_PAD_LEFT).".';";
        }

        if ($_REQUEST['inCodEspecie'] != '') {
            $stJs = "$('".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').value = '".str_pad($_REQUEST['inCodNatureza'],strlen($arMascara[0]),'0',STR_PAD_LEFT).".".str_pad($_REQUEST['inCodGrupo'],strlen($arMascara[1]),'0',STR_PAD_LEFT).".".str_pad($_REQUEST['inCodEspecie'],strlen($arMascara[1]),'0',STR_PAD_LEFT)."';";
        }

        break;
    case 'preencheCombos' :
        if ( strlen($_REQUEST['stCodClassificacao']) > strlen($arMascara[0]) ) {
            $arClassificacao = explode( '.',$_REQUEST['stCodClassificacao'] );

            //verifica e seta como selected a natureza
            $stJs.= "$('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obISelectNatureza->getId()."').selectedIndex = 0;";
            $stJs.= "$('".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').value = '';";
            $stJs.= "
                for (i=0;i<$('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obISelectNatureza->getId()."').length;i++) {
                   if ($('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obISelectNatureza->getId()."').options[i].value == '".(int) $arClassificacao[0]."') {
                       $('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obISelectNatureza->getId()."').selectedIndex = i;
                       $('".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').value = '".$arClassificacao[0].".';
                    }
                }
            ";

            //limpa o combo de grupos
            $stJs.= "limpaSelect($('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obSelectGrupo->getId()."'),0);";
            $stJs.= "$('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obSelectGrupo->getId()."').options[0] = new Option('Selecione','','selected');";

            //limpa o combo de especie
            $stJs.= "limpaSelect($('".$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->getId()."'),0);";
            $stJs.= "$('".$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->getName()."').options[0] = new Option('Selecione','','selected');";

            //recupera os grupos do banco
            $stFiltro = " WHERE cod_natureza = ".(int) $arClassificacao[0]." ";
            $obIMontaClassificacao->obTPatrimonioGrupo->recuperaTodos( $rsGrupo, $stFiltro );

            $inCount = 1;
            //inclui grupos no select e seta o selecionado
            while ( !$rsGrupo->eof() ) {
                //verifica se o registro é igual a chave e deixa ele selecionado no select
                if ( (int) $arClassificacao[1] == $rsGrupo->getCampo('cod_grupo') ) {
                    $stSelected = 'true';
                    $stJs.= "jq('#".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').val('".$arClassificacao[0].".".$arClassificacao[1].".');";
                } else {
                    $stSelected = 'false';
                }

                $stJs.= "jq('#".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obSelectGrupo->getId()."').addOption('".$rsGrupo->getCampo('cod_grupo')."','".$rsGrupo->getCampo('cod_grupo')." - ".addslashes($rsGrupo->getCampo('nom_grupo'))."',".$stSelected.");";
                $inCount++;
                $rsGrupo->proximo();

            }

            if ( strlen($_REQUEST['stCodClassificacao']) > strlen($arMascara[0].'.'.$arMascara[1]) ) {
                //recupera as especies
                $stFiltro.=" AND cod_grupo = ".(int) $arClassificacao[1]." ";
                $obIMontaClassificacao->obTPatrimonioEspecie->recuperaTodos( $rsEspecie, $stFiltro );

                $inCount = 1;
                //inclui especies no select e seta o selecionado
                while ( !$rsEspecie->eof() ) {
                    //verifica se o registro é igual a chave e deixa ele selecionado no select
                    if ( (int) $arClassificacao[2] == $rsEspecie->getCampo('cod_especie') ) {
                        $stSelected = 'true';
                        $stJs.= "jq('#".$obIMontaClassificacao->obTxtCodClassificacao->getId()."').val('".$arClassificacao[0].".".$arClassificacao[1].".".$arClassificacao[2]."');";
                    } else {
                        $stSelected = 'false';
                    }

                    $stJs.= "jq('#".$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->getId()."').addOption('".$rsEspecie->getCampo('cod_especie')."','".$rsEspecie->getCampo('cod_especie')." - ".addslashes($rsEspecie->getCampo('nom_especie'))."',".$stSelected.");";
                    $inCount++;
                    $rsEspecie->proximo();

                }
            }
        } else {
            $stJs.= "$('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obISelectNatureza->getId()."').selectedIndex = 0;";

            //limpa o combo de grupos
            $stJs.= "limpaSelect($('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obSelectGrupo->getName()."'),0);";
            $stJs.= "$('".$obIMontaClassificacao->obISelectEspecie->obISelectGrupo->obSelectGrupo->getName()."').options[0] = new Option('Selecione','','selected');";

            //limpa o combo de especie
            $stJs.= "limpaSelect($('".$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->getName()."'),0);";
            $stJs.= "$('".$obIMontaClassificacao->obISelectEspecie->obSelectEspecie->getName()."').options[0] = new Option('Selecione','','selected');";

        }

        break;
}

echo $stJs;
