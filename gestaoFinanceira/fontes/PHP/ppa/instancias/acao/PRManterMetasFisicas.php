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
 * Página de Processamento de Lançar Metas Fisicas Realizadas.
 * Data de Criacao: 15/04/2016

 * @author Analista : Valtair Santos
 * @author Desenvolvedor : Michel Teixeira
 * @ignore

 $Id: PRManterMetasFisicas.php 65085 2016-04-22 13:41:21Z michel $

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPAAcaoMetaFisicaRealizada.class.php';

# Define o nome dos arquivos PHP
$stPrograma = 'ManterMetasFisicas';
$pgFilt     = 'FL'.$stPrograma.'.php';
$pgList     = 'LS'.$stPrograma.'.php';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php';
$pgJs       = 'JS'.$stPrograma.'.js';

$obTransacao = new Transacao();
$boFlagTransacao = false;
$boTransacao = false;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

$arParametrosMetas = Sessao::read('arParametrosMetas');
$arParametrosMetas = is_array($arParametrosMetas) ? $arParametrosMetas : array();

foreach($arParametrosMetas AS $stCodAcaoRecurso => $metaRealizada){
    if($metaRealizada['boAlterado']){
        $obTPPAAcaoMetaFisicaRealizada = new TPPAAcaoMetaFisicaRealizada;

        $obTPPAAcaoMetaFisicaRealizada->setDado('cod_acao', $metaRealizada['cod_acao']);
        $obTPPAAcaoMetaFisicaRealizada->setDado('timestamp_acao_dados', $metaRealizada['timestamp_acao_dados']);
        $obTPPAAcaoMetaFisicaRealizada->setDado('cod_recurso', $metaRealizada['cod_recurso']);

        $obErro = $obTPPAAcaoMetaFisicaRealizada->exclusao($boTransacao);

        if (!$obErro->ocorreu()){
            for ($inAno = 1; $inAno < 5; $inAno++) {
                $stExercicio      = $metaRealizada['stExercicio_'.$inAno];
                $stJustificativa  = trim($metaRealizada['stJustificativa_'.$inAno]);
                $stJustificativa  = (htmlentities($stJustificativa)=='&nbsp;') ? '' : $stJustificativa;//Substituindo espaço padrão do textarea

                $flVlRealizado    = $metaRealizada['flValorRealizado_'.$inAno];
                $flValorRealizado = is_numeric($flVlRealizado) ? number_format($flVlRealizado, 2, ",", ".") : 0;
                $inPorcentagem    = ( $metaRealizada['flValorRealizado_'.$inAno] * 100 ) / $metaRealizada['flValorTotal_'.$inAno];

                $obTPPAAcaoMetaFisicaRealizada->setDado('ano'              , $inAno           );
                $obTPPAAcaoMetaFisicaRealizada->setDado('exercicio_recurso', $stExercicio     );
                $obTPPAAcaoMetaFisicaRealizada->setDado('justificativa'    , $stJustificativa );
                $obTPPAAcaoMetaFisicaRealizada->setDado('valor'            , $flValorRealizado);

                $stErro  = " Ação: ".$metaRealizada['num_acao'].",";
                $stErro .= " Recurso: ".$metaRealizada['cod_recurso'].",";
                $stErro .= " Ano: ".$inAno.".";

                if( $flVlRealizado == 0 && empty($stJustificativa) && $stExercicio <= Sessao::getExercicio() ){
                    $stErro .= " Obrigatório informar o campo Justificativa para Valor Realizado igual a zero. ";
                    $obErro->setDescricao($stErro);
                }elseif( ( ($inPorcentagem > 0 && $inPorcentagem < 90) OR $inPorcentagem > 110) && empty($stJustificativa) && $stExercicio <= Sessao::getExercicio() ){
                    $stErro .= " Obrigatório informar o campo Justificativa quando o campo Valor Realizado possui 10% de diferença do campo Valor Previsto. ";
                    $obErro->setDescricao($stErro);
                }else{
                    $obErro = $obTPPAAcaoMetaFisicaRealizada->inclusao($boTransacao);
                }

                if ($obErro->ocorreu()){
                    break;
                }
            }
        }
    }

    if ($obErro->ocorreu())
        break;
}

if ($obErro->ocorreu())
    SistemaLegado::exibeAviso(urlencode("Erro ao Lançar Metas Fisicas Realizadas (".$obErro->getDescricao().")"),"","erro");
else
    SistemaLegado::alertaAviso($pgForm,"Lançar Metas Fisicas Realizadas Concluído com Sucesso!","","aviso", Sessao::getId(), "../");

$obErro = $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTPPAAcaoMetaFisicaRealizada );

SistemaLegado::LiberaFrames();
?>