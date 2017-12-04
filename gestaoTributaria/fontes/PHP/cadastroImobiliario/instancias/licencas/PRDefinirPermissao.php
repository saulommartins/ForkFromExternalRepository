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
  * Página de Processamento da Definir Permissao para Conceder Licenca
  * Data de criação : 17/03/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRDefinirPermissao.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso: uc-05.01.28
**/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMPermissao.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "DefinirPermissao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "definir":
        $arPermissoesSessao = Sessao::read('permissoes');
        if ( count($arPermissoesSessao) <= 0 ) {
            SistemaLegado::exibeAviso( "A lista de permissões está vazia.", "n_definir", "erro" );
            exit;
        }

        $obTCIMPermissao = new TCIMPermissao;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMPermissao );

            for ( $inX=0; $inX<count($arPermissoesSessao); $inX++ ) {
                $obTCIMPermissao->setDado( "cod_tipo", $arPermissoesSessao[$inX]["inTipoLicenca"] );
                $obTCIMPermissao->setDado( "numcgm",   $arPermissoesSessao[$inX]["inCGM"] );
                $obTCIMPermissao->inclusao();
            }

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgForm, "Permissões definidas com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;
}
