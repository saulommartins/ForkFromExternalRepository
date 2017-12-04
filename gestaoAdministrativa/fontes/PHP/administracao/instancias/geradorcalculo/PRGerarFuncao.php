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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RBiblioteca.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RModulo.class.php"     );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastro.class.php"   );

$stPrograma = "GerarFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRBiblioteca = Sessao::read('obRBiblioteca');
switch ( $request->get('stAcao') ) {
    case "incluir":
        $obRBiblioteca->setCodigoBiblioteca( $request->get('inCodBiblioteca') );
        $arAtributoComFunc = $request->get('arAtributoComFunc');
        if ( is_array( $arAtributoComFunc ) ) {
            foreach ( $arAtributoComFunc  as $inIndice => $inCodAtributo) {
                $obRBiblioteca->addFuncao();
                $obRBiblioteca->roRAtributoFuncao->roRAtributoDinamico->setCodAtributo( $inCodAtributo );
            }
        }
        $obErro = $obRBiblioteca->salvarFuncoes();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Funções geradas: ".Sessao::read('stNomeFuncoes')." ","incluir","aviso", Sessao::getId(), "../");
            Sessao::remove('stNomeFuncoes');
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}

?>
