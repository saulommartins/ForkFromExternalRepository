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
    * Página de Processamento - Parâmetros do Arquivo
    * Data de Criação   : 24/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 62952 $
    $Name$
    $Autor: $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTBATipoBem.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoBem";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');


switch ($stAcao) {
    case 'manter' :
        
        $obErro = new Erro();
        $obTTBATipoBem = new TTBATipoBem();
        $obTransacao   = new Transacao();
        
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        
        $boFlagTransacao = false;
        
        $obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        
        $obTTBATipoBem->recuperaTodos( $rsExclusao );
        
        while ( !$rsExclusao->eof() ) {
            $obTTBATipoBem->setDado( 'cod_tipo_tcm', $rsExclusao->getCampo('cod_tipo_tcm') );
            $obTTBATipoBem->exclusao($boTransacao);
            $rsExclusao->proximo();
        }
    
        foreach ($_POST as $key => $value) {
            if (strstr($key,"inTipo")) {
                $arIdentificador = explode('_',$key);
                $inCod = $arIdentificador[1];
                if (trim($value) <> "") {
                    $arNaturezaGrupo = explode( "_", $value );
                    $obTTBATipoBem->setDado('cod_tipo_tcm' ,$value);
                    $obTTBATipoBem->setDado('cod_natureza' ,$arIdentificador[1]);
                    $obTTBATipoBem->setDado('cod_grupo'    ,$arIdentificador[2]);
                    $obErro = $obTTBATipoBem->inclusao($boTransacao);
                    if( $obErro->ocorreu() )
                        break;
                }
            }
        }
        
        if ( !$obErro->ocorreu() ) {
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro ,$obTTBATipoBem);
            SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        

    break;
}
    
?>