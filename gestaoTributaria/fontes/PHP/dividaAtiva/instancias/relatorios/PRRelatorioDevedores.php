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
* Página de geração de relatório
* Data de criação : 10/05/2012

Caso de uso: uc-03.01.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

include_once CAM_GT_DAT_MAPEAMENTO."TARRRelatorioDevedores.class.php";
include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CLA_MPDF;

//Define o nome dos arquivos PHP
$stPrograma      = 'RelatorioDevedores';
$pgFilt          = 'FL'.$stPrograma.'.php';
$pgList          = 'LS'.$stPrograma.'.php';
$pgForm          = 'FM'.$stPrograma.'.php';
$pgProc          = 'PR'.$stPrograma.'.php';
$pgOcul          = 'OC'.$stPrograma.'.php';
$pgGera          = 'OCGera'.$stPrograma.'.php';
$pgJs            = 'JS'.$stPrograma.'.js';
include_once( $pgJs );

$obTARRRelatorioDevedores = new TARRRelatorioDevedores;
$rsRegistros = new Recordset;

$arListaCredito = Sessao::read("arListaCredito");
$arListaGrupoCredito = Sessao::read("arListaGrupoCredito");

if($_REQUEST['stFiltro'] == 'credito') {
    if ( count($arListaCredito) != 0 ) {
        foreach ( $arListaCredito as $arCredito) {
            $credito = explode('.', $arCredito["stCodCredito"]);
                    
            $obTARRRelatorioDevedores->setDado('cod_credito' , $credito[0]);
            $obTARRRelatorioDevedores->setDado('cod_especie' , $credito[1]);
            $obTARRRelatorioDevedores->setDado('cod_genero'  , $credito[2]);
            $obTARRRelatorioDevedores->setDado('cod_natureza', $credito[3]);
            $obTARRRelatorioDevedores->setDado('exercicio'   , $arCredito["stExercicio"]);
            $obTARRRelatorioDevedores->setDado('limite'      , $_REQUEST['inLimite']);
            $obTARRRelatorioDevedores->consultaPorCredito($rsRegistros);
            
            $registros = array(
                'codigo'    => $arCredito["stCodCredito"],
                'descricao' => $arCredito["stCreditoDescricao"],
                'dados'     => $rsRegistros->getElementos()
            );
            
            $arRecordSetRegistros[] = $registros;
        }
    }else{
        SistemaLegado::LiberaFrames(true,false);
        SistemaLegado::alertaAviso($pgForm."?stFiltro=credito","Deve ser incluso pelo menos um Crédito","n_incluir","erro",Sessao::getId(),"../");
        exit();
    }
} else {
    if ( count($arListaGrupoCredito) != 0 ) {
        foreach ($arListaGrupoCredito as $arGrupo) {
            $grupo = explode('/', $arGrupo["stCodGrupo"]);
                    
            $obTARRRelatorioDevedores->setDado('cod_grupo', $grupo[0]);
            $obTARRRelatorioDevedores->setDado('exercicio', $grupo[1]);
            $obTARRRelatorioDevedores->setDado('limite'   , $_REQUEST['inLimite']);
            $obTARRRelatorioDevedores->consultaPorGrupo($rsRegistros);
            
            $registros = array(
                'codigo'    => $arGrupo["stCodGrupo"],
                'descricao' => $arGrupo["stGrupoDescricao"],
                'dados'     => $rsRegistros->getElementos()
            );
            
            $arRecordSetRegistros[] = $registros;
        }
    }else{
        SistemaLegado::LiberaFrames(true,false);
        SistemaLegado::alertaAviso($pgForm,"Deve ser incluso pelo menos um Grupo de Crédito","n_incluir","erro",Sessao::getId(),"../");
        exit();
    }
}

Sessao::write('arRegistros', $arRecordSetRegistros);

SistemaLegado::LiberaFrames(true,true);

SistemaLegado::mudaFramePrincipal(CAM_GT_DAT_INSTANCIAS."relatorios/OCGeraRelatorioDevedores.php");