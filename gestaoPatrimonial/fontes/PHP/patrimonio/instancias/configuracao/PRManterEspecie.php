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
    * Data de Criação: 06/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26982 $
    $Name$
    $Author: bruce $
    $Date: 2007-11-29 16:42:18 -0200 (Qui, 29 Nov 2007) $

    * Casos de uso: uc-03.01.05
*/

/*
$Log$
Revision 1.3  2007/10/05 12:59:27  hboaventura
inclusão dos arquivos

Revision 1.2  2007/09/27 12:57:13  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:11  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"           );
$stPrograma = "ManterEspecie";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRCadastroDinamico = new RCadastroDinamico();
$obTPatrimonioEspecie = new TPatrimonioEspecie();
$obTPatrimonioEspecieAtributo = new TPatrimonioEspecieAtributo();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioEspecie );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioEspecieAtributo );

switch ($stAcao) {
    case 'incluir':
        $obTPatrimonioEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
        $obTPatrimonioEspecie->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
        $obTPatrimonioEspecie->setDado( 'nom_especie', $_REQUEST['stDescricaoEspecie'] );
        $obTPatrimonioEspecie->recuperaEspecie( $rsPatrimonioEspecie );

        if ( $rsPatrimonioEspecie->getNumLinhas() <= 0 ) {
            $obTPatrimonioEspecie->proximoCod( $inCodEspecie );
            $obTPatrimonioEspecie->setDado( 'cod_especie'   , $inCodEspecie );

            $obTPatrimonioEspecie->inclusao();

            $obTPatrimonioEspecieAtributo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_especie', $inCodEspecie );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_modulo', 6);
            $obTPatrimonioEspecieAtributo->setDado( 'cod_cadastro',1);

            $obTPatrimonioEspecieAtributo->recuperaEspecieAtributo( $rsAtributoEspecie );

            $arInativas = array();

            //recupera todas os atributos para a chave e seta ativo = false
            while ( !$rsAtributoEspecie->eof() ) {
                $obTPatrimonioEspecieAtributo->setDado( 'cod_atributo',$rsAtributoEspecie->getCampo( 'cod_atributo' ) );
                $obTPatrimonioEspecieAtributo->setDado( 'ativo', 'false' );
                $obTPatrimonioEspecieAtributo->alteracao();

                $arInativas[] = $rsAtributoEspecie->getCampo( 'cod_atributo' );

                $rsAtributoEspecie->proximo();
            }

            //para cada atributo selecionado, se existir o cadastro, ativa, se não
            //efetua o cadastro
            if ( is_array( $_REQUEST['inCodAtributosSelecionados'] ) ) {
                $obTPatrimonioEspecieAtributo->setDado( 'ativo', 'true' );
                foreach ($_REQUEST['inCodAtributosSelecionados'] as $inCodAtributo) {
                    $obTPatrimonioEspecieAtributo->setDado( 'cod_atributo', $inCodAtributo );
                    if ( !in_array($inCodAtributo, $arInativas ) ) {
                        $obTPatrimonioEspecieAtributo->inclusao();
                    } else {
                        $obTPatrimonioEspecieAtributo->alteracao();
                    }
                }
            }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Espécie - $inCodEspecie ". $_REQUEST['stDescricaoEspecie']  ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe uma espécie com esta descrição para este grupo'),"n_incluir","erro");
        }

        break;

    case 'alterar' :

        $obTPatrimonioEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
        $obTPatrimonioEspecie->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
        $obTPatrimonioEspecie->setDado( 'nom_especie', $_REQUEST['stDescricaoEspecie'] );

        $stFiltro = " AND  especie.cod_especie <> ".$_REQUEST['inCodEspecie']." ";

        $obTPatrimonioEspecie->recuperaEspecie( $rsPatrimonioEspecie, $stFiltro );

        if ( $rsPatrimonioEspecie->getNumLinhas() <= 0 ) {
            $obTPatrimonioEspecie->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );

            $obTPatrimonioEspecie->alteracao();

            $obTPatrimonioEspecieAtributo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_grupo', $_REQUEST['inCodGrupo'] );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_especie', $_REQUEST['inCodEspecie'] );
            $obTPatrimonioEspecieAtributo->setDado( 'cod_modulo', 6);
            $obTPatrimonioEspecieAtributo->setDado( 'cod_cadastro',1);

            $obTPatrimonioEspecieAtributo->recuperaEspecieAtributo( $rsAtributoEspecie );
            $arInativas = array();

            //recupera todas os atributos para a chave e seta ativo = false
            while ( !$rsAtributoEspecie->eof() ) {
                $obTPatrimonioEspecieAtributo->setDado( 'cod_atributo',$rsAtributoEspecie->getCampo( 'cod_atributo' ) );
                $obTPatrimonioEspecieAtributo->setDado( 'ativo', 'false' );
                $obTPatrimonioEspecieAtributo->alteracao();

                $arInativas[] = $rsAtributoEspecie->getCampo( 'cod_atributo' );

                $rsAtributoEspecie->proximo();
            }

            //para cada atributo selecionado, se existir o cadastro, ativa, se não
            //efetua o cadastro
            if ( is_array( $_REQUEST['inCodAtributosSelecionados'] ) ) {
                foreach ($_REQUEST['inCodAtributosSelecionados'] as $inCodAtributo) {
                    $obTPatrimonioEspecieAtributo->setDado( 'cod_atributo', $inCodAtributo );
                    if ( !in_array($inCodAtributo, $arInativas ) ) {
                        $obTPatrimonioEspecieAtributo->setDado( 'ativo', 'true' );
                        $obTPatrimonioEspecieAtributo->inclusao();
                    } else {
                        $obTPatrimonioEspecieAtributo->setDado( 'ativo', 'true' );
                        $obTPatrimonioEspecieAtributo->alteracao();
                    }
                }
            }

            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Espécie - ".$_REQUEST['inCodEspecie'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe uma espécie com esta descrição para este grupo'),"n_incluir","erro");
        }
        break;

    case 'excluir' :

        $obTPatrimonioEspecieAtributo->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
        $obTPatrimonioEspecieAtributo->setDado( 'cod_grupo'   , $_REQUEST['inCodGrupo']	   );
        $obTPatrimonioEspecieAtributo->setDado( 'cod_especie' , $_REQUEST['inCodEspecie']  );
        $obTPatrimonioEspecieAtributo->exclusao();

        $obTPatrimonioEspecie->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
        $obTPatrimonioEspecie->setDado( 'cod_grupo'   , $_REQUEST['inCodGrupo']    );
        $obTPatrimonioEspecie->setDado( 'cod_especie' , $_REQUEST['inCodEspecie']  );
        $obTPatrimonioEspecie->exclusao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Espécie - ".$_REQUEST['inCodEspecie'],"excluir","aviso", Sessao::getId(), "../");

        break;

}
Sessao::encerraExcecao();
