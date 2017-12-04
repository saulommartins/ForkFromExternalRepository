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
    * Página de Processamento de COPIA DE GRUPO DE CREDITO
    * Data de Criação   : 23/05/2005

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou

    * @ignore

    * $Id: PRManterCopiarGrupo.php 62471 2015-05-13 12:49:12Z michel $

* Casos de uso: uc-05.03.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_ARR_MAPEAMENTO.'TARRGrupoCredito.class.php';
include_once CAM_GT_ARR_NEGOCIO.'RARRGrupo.class.php';
include_once CAM_GT_ARR_MAPEAMENTO.'TARRRegraDesoneracaoGrupo.class.php';
include_once CAM_GT_ARR_MAPEAMENTO.'TARRCreditoGrupo.class.php';

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCopiarGrupo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

switch ($stAcao) {
    case "alterar":
        $obTARRGrupoCredito = new TARRGrupoCredito;
        $obRARRGrupo = new RARRGrupo;
        
        $stFiltro = " WHERE ano_exercicio = '".$_REQUEST["inNovoExercicio"]."'";

        if ($_REQUEST["cmbGrupos"] > 0) {
            $stFiltro .= " AND cod_grupo = ".$_REQUEST["cmbGrupos"];
        }

        $obTARRGrupoCredito->recuperaTodos ( $rsListaGrupos, $stFiltro );

        if ( !$rsListaGrupos->eof() ) {
            sistemaLegado::alertaAviso( "FMManterCopiarGrupo.php?".Sessao::getId()."&stAcao=alterar","Exercício de destino já está cadastrado na base de dados.","n_erro","erro",Sessao::getId(), "../");
            exit;
        }

        $stFiltro = " WHERE ano_exercicio = '".$_REQUEST["cmbExercicio"]."'";

        if ($_REQUEST["cmbGrupos"] > 0) {
            $stFiltro .= " AND cod_grupo = ".$_REQUEST["cmbGrupos"];
        }

        $obTARRGrupoCredito->recuperaTodos( $rsListaGrupos, $stFiltro );

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRGrupoCredito );

        foreach ($rsListaGrupos->getElementos() as $i => $val ) {
            $obTARRGrupoCredito->setDado( "cod_grupo", $val["cod_grupo"] );
            $obTARRGrupoCredito->setDado( "ano_exercicio", $_REQUEST["inNovoExercicio"] );
            $obTARRGrupoCredito->setDado( "cod_modulo", $val["cod_modulo"] );
            $obTARRGrupoCredito->setDado( "descricao", $val["descricao"] );
            $obTARRGrupoCredito->inclusao();
            
            $obTARRRegraDesoneracaoGrupo = new TARRRegraDesoneracaoGrupo;
            $obTARRRegraDesoneracaoGrupo->setDado ( "cod_grupo", $val["cod_grupo"] );
            $obTARRRegraDesoneracaoGrupo->setDado ( "ano_exercicio", $_REQUEST["cmbExercicio"] );
            $obTARRRegraDesoneracaoGrupo->recuperaPorChave($rsLista);

            foreach ($rsLista->getElementos() as $key => $value ) {
                $obTARRRegraDesoneracaoGrupo = new TARRRegraDesoneracaoGrupo;
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_grupo"     , $val["cod_grupo"]             );
                $obTARRRegraDesoneracaoGrupo->setDado ( "ano_exercicio" , $_REQUEST["inNovoExercicio"]  );
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_modulo"    , $value["cod_modulo"]          );
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_biblioteca", $value["cod_biblioteca"]      );
                $obTARRRegraDesoneracaoGrupo->setDado ( "cod_funcao"    , $value["cod_funcao"]          );
                $obTARRRegraDesoneracaoGrupo->inclusao();
                
                $rsLista->proximo();
            }
            
            $obRARRGrupo->setCodGrupo( $val["cod_grupo"] );
            $obRARRGrupo->setExercicio( $_REQUEST["cmbExercicio"] );
            $obRARRGrupo->listarCreditos($rsCreditos);
            
            foreach ($rsCreditos->getElementos() as $key => $value ) {
                $obTARRCreditoGrupo = new TARRCreditoGrupo;                
                $obTARRCreditoGrupo->setDado ( "cod_grupo"      , $val["cod_grupo"]             );
                $obTARRCreditoGrupo->setDado ( "cod_credito"    , $value["cod_credito"]         );
                $obTARRCreditoGrupo->setDado ( "cod_especie"    , $value["cod_especie"]         );
                $obTARRCreditoGrupo->setDado ( "cod_genero"     , $value["cod_genero"]          );
                $obTARRCreditoGrupo->setDado ( "cod_natureza"   , $value["cod_natureza"]        );
                $obTARRCreditoGrupo->setDado ( "ordem"          , $value["ordem"]  		        );
                $obTARRCreditoGrupo->setDado ( "desconto"       , $value["desconto"]            );
                $obTARRCreditoGrupo->setDado ( "ano_exercicio"  , $_REQUEST["inNovoExercicio"]  );
                $obTARRCreditoGrupo->inclusao();
                
                $rsCreditos->proximo();
            }
            
            $rsListaGrupos->proximo();
        }

        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso( "FMManterCopiarGrupo.php?".Sessao::getId()."&stAcao=alterar","Total de Grupos: ".$rsListaGrupos->getNumLinhas(),"incluir","aviso", Sessao::getId(), "../" );
    break;
}
?>
