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
    * Classe de Regra de Negócios para relatorio de Razao de Empenho
    * Data de Criação: 02/06/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30805 $
    $Name$
    $Author: vitor $
    $Date: 2007-04-05 16:00:44 -0300 (Qui, 05 Abr 2007) $

    * Casos de uso: uc-02.03.14
*/

/*
$Log$
Revision 1.11  2007/04/05 19:00:44  vitor
#8926#

Revision 1.10  2007/02/13 12:27:40  luciano
#8381#

Revision 1.9  2006/07/24 20:14:25  cako
Bug #6555#

Revision 1.8  2006/07/05 20:47:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE_RELATORIO);
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );

/**
    * Classe de Regra de Negócios Modelos Executivo
    * @author Desenvolvedor: Lucas Leusin Oaigen
*/
class REmpenhoRelatorioRazaoEmpenho extends PersistenteRelatorio
{
/*
    * @var Array
    * @access Private
*/

var $arItens = array();
/**
    * @var Object
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Object
    * @access Private
*/
var $inCodEmpenho;
/**
    * @var Integer
    * @access Private
*/
var $inExercicio;
/**
    * @var String
    * @access Private
*/

/*
    * @access Public
    * @param Object $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade      = $valor; }
/*
    * @access Public
    * @param Object $valor
*/
function setArItens($valor) { $this->arItens            = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setCodEmpenho($valor) { $this->inCodEmpenho      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setExercicio($valor) { $this->inExercicio        = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getCodEntidade() { return $this->inCodEntidade;                     }
/**
     * @access Public
     * @param Object $valor
*/
function getArItens() { return $this->arItens;                     }
/**
     * @access Public
     * @param Object $valor
*/
function getCodEmpenho() { return $this->inCodEmpenho;                 }
/**
     * @access Public
     * @return Object
*/
function getExercicio() { return $this->inExercicio;                   }

/**
     * @access Public
     * @return Object
*/
function REmpenhoRelatorioRazaoEmpenho()
{
    $this->obRRelatorio                 = new RRelatorio;
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet, &$rsRecordSet1, &$rsRecordSet2, &$rsRecordSet3, &$rsRecordSet4, &$rsRecordSet5, &$rsRecordSet6, &$rsRecordLinha, $stOrder = "")
{
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php"   );
    $obTEmpenhoEmpenho                  = new TEmpenhoEmpenho;

    $stFiltro = "";
    $inCount                = 0;
    $arRecord           = array();
    $arRecord1          = array();
    $arRecord2          = array();
    $arRecord3          = array();
    $arRecord4          = array();
    $arRecord5          = array();
    $arRecord6          = array();

    $obTEmpenhoEmpenho->setDado("inCodEmpenho",$this->getCodEmpenho());
    $obTEmpenhoEmpenho->setDado("inExercicio",$this->getExercicio());
    $obTEmpenhoEmpenho->setDado("inCodEntidade",$this->getCodEntidade());

    $obErro = $obTEmpenhoEmpenho->recuperaRelatorioRazaoEmpenho( $rsRecordSet, $stFiltro, $stOrder );

    if ( !$obErro->ocorreu() ) {

        if (!$rsRecordSet->eof()) {

            $arRecord[0]['coluna1']  = "Número do Empenho:";
            $arRecord[0]['coluna2']  = $rsRecordSet->getCampo('cod_empenho') . '/' . $rsRecordSet->getCampo('exercicio');
            $arRecord[0]['coluna3']  = "Emissão:";
            $arRecord[0]['coluna4']  = $rsRecordSet->getCampo('dt_empenho');
            $arRecord[0]['coluna5']  = "Validade:";
            $arRecord[0]['coluna6']  = $rsRecordSet->getCampo('dt_vencimento');

            $arRecord1[0]['coluna1'] = "Entidade:";
            $arRecord1[0]['coluna2'] = $rsRecordSet->getCampo('cod_entidade');
            $arRecord1[0]['coluna3'] = $rsRecordSet->getCampo('nom_entidade');

            $arRecord1[1]['coluna1'] = "Orgão:";
            $arRecord1[1]['coluna2'] = $rsRecordSet->getCampo('num_orgao');
            $arRecord1[1]['coluna3'] = $rsRecordSet->getCampo('nom_orgao');

            $arRecord1[2]['coluna1'] = "Unidade:";
            $arRecord1[2]['coluna2'] = $rsRecordSet->getCampo('num_unidade');
            $arRecord1[2]['coluna3'] = $rsRecordSet->getCampo('nom_unidade');

            $arRecord1[3]['coluna1'] = "Dotação:";
            $arRecord1[3]['coluna2'] = $rsRecordSet->getCampo('cod_despesa');
            $arRecord1[3]['coluna3'] = $rsRecordSet->getCampo('cod_estrutural_dot'). "   " . $rsRecordSet->getCampo('descricao_dot');

            $arRecord1[4]['coluna1'] = "Desdobramento:";
            $arRecord1[4]['coluna2'] = "";
            $arRecord1[4]['coluna3'] = $rsRecordSet->getCampo('cod_estrutural_desdobramento') . "   " . $rsRecordSet->getCampo('descricao_desdobramento');

            $arRecord1[5]['coluna1'] = "PAO:";
            $arRecord1[5]['coluna2'] = $rsRecordSet->getCampo('num_acao');
            $arRecord1[5]['coluna3'] = $rsRecordSet->getCampo('nom_pao');

            $arRecord1[6]['coluna1'] = "Recurso:";
            $arRecord1[6]['coluna2'] = $rsRecordSet->getCampo('cod_recurso');
            $arRecord1[6]['coluna3'] = $rsRecordSet->getCampo('nom_recurso');
            $arRecord1[6]['coluna4'] = "";

            $arRecord1[7]['coluna1'] = "Histórico Padrão:";
            $arRecord1[7]['coluna2'] = $rsRecordSet->getCampo('cod_historico');
            $arRecord1[7]['coluna3'] = $rsRecordSet->getCampo('nom_historico');
            $arRecord1[7]['coluna4'] = "";

            $arRecord2[0]['coluna1'] = "Credor:";
            $arRecord2[0]['coluna2'] = $rsRecordSet->getCampo('num_cgm')." - ".$rsRecordSet->getCampo('nom_fornecedor');
            $arRecord2[1]['coluna1'] = "Endereço";
            $arRecord2[1]['coluna2'] = $rsRecordSet->getCampo('endereco');
            $arRecord2[2]['coluna1'] = "Munic/UF:";
            $arRecord2[2]['coluna2'] = $rsRecordSet->getCampo('municipio_uf');
            $arRecord2[3]['coluna1'] = "CEP/Fone:";
            $arRecord2[3]['coluna2'] = $rsRecordSet->getCampo('cep').'/'.$rsRecordSet->getCampo('fone');

            $arRecord3[0]['coluna1'] = "Autorização n.:";
            $arRecord3[0]['coluna2'] = $rsRecordSet->getCampo('cod_autorizacao');
            $arRecord3[0]['coluna3'] = "Valor Empenhado:";
            $arRecord3[0]['coluna4'] = number_format($rsRecordSet->getCampo('vl_empenhado'),2,",", ".");
            $arRecord3[1]['coluna1'] = "Ordem de Compra:";
            $arRecord3[1]['coluna2'] = "";
            $arRecord3[1]['coluna3'] = "Valor Anulado:";
            $arRecord3[1]['coluna4'] = number_format($rsRecordSet->getCampo('vl_empenhado_anulado'),2,",", ".");
            $arRecord3[2]['coluna1'] = "Licitação:";
            $arRecord3[2]['coluna2'] = "";
            $arRecord3[2]['coluna3'] = "Valor Liquidado:";
            $arRecord3[2]['coluna4'] = number_format($rsRecordSet->getCampo('vl_liquidado'),2,",", ".");
            $arRecord3[3]['coluna1'] = "";
            $arRecord3[3]['coluna2'] = "";
            $arRecord3[3]['coluna3'] = "Valor Pago:";
            $arRecord3[3]['coluna4'] = number_format($rsRecordSet->getCampo('vl_pago'),2,",", ".");
            $arRecord3[4]['coluna1'] = "";
            $arRecord3[4]['coluna2'] = "";
            $arRecord3[4]['coluna3'] = "Saldo Atual:";
            $arRecord3[4]['coluna4'] = number_format($rsRecordSet->getCampo('saldo_atual'),2,",", ".");

            $stDescTemp = str_replace( chr(10), "", $rsRecordSet->getCampo('descricao'));
            $stDescTemp = wordwrap( $stDescTemp,110,chr(13) );
            $arDescOLD = explode( chr(13), $stDescTemp );

            $inCountQuebra=0;
            foreach ($arDescOLD as $stDescTemp) {
                $arRecord4[$inCountQuebra]['coluna1'] = $stDescTemp;
                $inCountQuebra++;
            }

            $arRecordLinha[0]['coluna1'] = "_____________________________________________________________________________________________________________";
        }
        $inCount=0;
        foreach ( $this->getArItens() as $obItem ) {
            $stDescItem = $obItem['nom_item']." ".$obItem['complemento'];
            $stDescTemp = str_replace( chr(10), "", $stDescItem );
            $stDescTemp = str_replace( chr(13), "", $stDescItem );

            $stDescTemp = wordwrap( $stDescTemp,68,chr(13) );
            $arDescOLD = explode( chr(13), $stDescTemp );

            $inCountQuebra=$inCount;
            foreach ($arDescOLD as $stDescTemp) {
                $arRecord6[$inCountQuebra]['coluna1'] = $stDescTemp;
                $inCountQuebra++;
            }
            if($obItem['vl_total']=="") $obItem['vl_total']='0.00';
            if($obItem['vl_liquidado']=="") $obItem['vl_liquidado']='0.00';

            $arRecord6[$inCount]['coluna2'] = $obItem['vl_total'];
            $arRecord6[$inCount]['coluna3'] = $obItem['vl_liquidado'];
            $inCount++;
        }

    }
    $obErro = $obTEmpenhoEmpenho->recuperaRelatorioRazaoEmpenhoLancamentos( $rsRecordSet2, $stFiltro, $stOrder );
    $inCount = 0;
    if ( !$obErro->ocorreu() ) {
        while ( !$rsRecordSet2->eof()) {
            $arRecord5[$inCount]['data']           = $rsRecordSet2->getCampo('data');
            $arRecord5[$inCount]['historico']      = $rsRecordSet2->getCampo('historico');
            $arRecord5[$inCount]['complemento']    = ltrim($rsRecordSet2->getCampo('complemento'));
            $arRecord5[$inCount]['valor']          = number_format($rsRecordSet2->getCampo('valor'),2,',','.');
            $arRecord5[$inCount]['debito']         = $rsRecordSet2->getCampo('debito');
            $arRecord5[$inCount]['credito']        = $rsRecordSet2->getCampo('credito');
            $inCount++;
            $rsRecordSet2->proximo();
        }
    }

    $rsRecordSet       = new RecordSet;
    $rsRecordSet1      = new RecordSet;
    $rsRecordSet2      = new RecordSet;
    $rsRecordSet3      = new RecordSet;
    $rsRecordSet4      = new RecordSet;
    $rsRecordSet5      = new RecordSet;
    $rsRecordSet6      = new RecordSet;
    $rsRecordLinha     = new RecordSet;

    $rsRecordSet->preenche( $arRecord );
    $rsRecordSet1->preenche( $arRecord1 );
    $rsRecordSet2->preenche( $arRecord2 );
    $rsRecordSet3->preenche( $arRecord3 );
    $rsRecordSet4->preenche( $arRecord4 );
    $rsRecordSet5->preenche( $arRecord5 );
    $rsRecordSet6->preenche( $arRecord6 );
    $rsRecordLinha->preenche( $arRecordLinha );
    $rsRecordSet6->addFormatacao("coluna2","NUMERIC_BR_NULL");
    $rsRecordSet6->addFormatacao("coluna3","NUMERIC_BR_NULL");

    //$rsRecordSet->debug();
    //die();
    return $obErro;
}

}
