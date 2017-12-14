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
    * Oculto do Componente FiltroAssentamentoMultiplo
    * Data de Criação: 21/02/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.00

    $Id: OCFiltroAssentamentoMultiplo.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencherAssentamentos()
{
    $stJs .= "limpaSelect(f.inCodAssentamentoDisponiveis,0);                              \n";
    //echo "alert('".$_REQUEST['inCodClassificacaoAssentamentoSelecionados'][0]."');";
    //print_r($_REQUEST);
    if ( is_array($_REQUEST['inCodClassificacaoAssentamentoSelecionados']) ) {
        $stCodClassificacaoAssentamento = implode(",",$_REQUEST['inCodClassificacaoAssentamentoSelecionados']);
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
        $obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
        $stFiltro = " WHERE cod_classificacao IN (".$stCodClassificacaoAssentamento.")";
        $obTPessoalAssentamentoAssentamento->recuperaTodos($rsAssentamentos,$stFiltro," descricao");

        $inIndex = 0;
        while ( !$rsAssentamentos->eof() ) {
            $stJs .= "f.inCodAssentamentoDisponiveis[".$inIndex."] = new Option('".$rsAssentamentos->getCampo('descricao')."','".$rsAssentamentos->getCampo('cod_assentamento')."','');\n";
            $inIndex++;
            $rsAssentamentos->proximo();
        }
    }

    return $stJs;
}

switch ($_REQUEST["stTipoBusca"]) {
    case "preencherAssentamentos":
        $stJs .= preencherAssentamentos();
        break;
}
if ($stJs) {
    //echo $stJs;
    sistemaLegado::executaFrameOculto($stJs);
}
?>
