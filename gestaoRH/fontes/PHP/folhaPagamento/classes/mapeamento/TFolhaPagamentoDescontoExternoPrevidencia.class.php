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
    * Classe de mapeamento da tabela folhapagamento.desconto_externo_previdencia
    * Data de Criação: 12/09/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31094 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-13 13:11:28 -0300 (Qui, 13 Set 2007) $

    * Casos de uso: uc-04.05.59
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.desconto_externo_previdencia
  * Data de Criação: 12/09/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoDescontoExternoPrevidencia extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoDescontoExternoPrevidencia()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.desconto_externo_previdencia");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,timestamp');

    $this->AddCampo('cod_contrato'       ,'integer'      ,true  ,''      ,true,'TPessoalContrato');
    $this->AddCampo('timestamp'          ,'timestamp_now',true  ,''      ,true,false);
    $this->AddCampo('vl_base_previdencia','numeric'      ,true  ,'14,2'  ,false,false);
    $this->AddCampo('vigencia'           ,'date'         ,true  ,''      ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql = "SELECT desconto_externo_previdencia.*  \n";
    $stSql .= "     , to_char(desconto_externo_previdencia.vigencia,'dd/mm/yyyy') as vigencia_formatado  \n";
    $stSql .= "     , to_real(desconto_externo_previdencia.vl_base_previdencia) as vl_base_previdencia_formatado  \n";
    $stSql .= "     , to_real(desconto_externo_previdencia_valor.valor_previdencia) as valor_previdencia  \n";
    $stSql .= "     , servidor.numcgm \n";
    $stSql .= "     , (SELECT sw_cgm.nom_cgm FROM sw_cgm where numcgm = servidor.numcgm) as nom_cgm  \n";
    $stSql .= "     , contrato.registro   \n";
    $stSql .= "  FROM folhapagamento.desconto_externo_previdencia \n";
    $stSql .= "     , (SELECT max(timestamp) as timestamp \n";
    $stSql .= "         , cod_contrato \n";
    $stSql .= "      FROM folhapagamento.desconto_externo_previdencia \n";
    $stSql .= "     WHERE NOT EXISTS (SELECT 1  \n";
    $stSql .= "                FROM folhapagamento.desconto_externo_previdencia_anulado \n";
    $stSql .= "               WHERE desconto_externo_previdencia_anulado.cod_contrato = desconto_externo_previdencia.cod_contrato \n";
    $stSql .= "                 AND desconto_externo_previdencia_anulado.timestamp = desconto_externo_previdencia.timestamp) \n";
    $stSql .= "       AND vigencia = (SELECT max(vigencia) FROM folhapagamento.desconto_externo_previdencia a1 WHERE a1.cod_contrato = desconto_externo_previdencia.cod_contrato) \n";
    $stSql .= "     GROUP BY cod_contrato) as max_desconto \n";
    $stSql .= "       LEFT JOIN folhapagamento.desconto_externo_previdencia_valor \n";
    $stSql .= "         ON desconto_externo_previdencia_valor.timestamp = max_desconto.timestamp \n";
    $stSql .= "        AND desconto_externo_previdencia_valor.cod_contrato = max_desconto.cod_contrato  \n";
    $stSql .= "       LEFT JOIN pessoal.contrato \n";
    $stSql .= "         ON contrato.cod_contrato = max_desconto.cod_contrato \n";
    $stSql .= "       LEFT JOIN pessoal.servidor_contrato_servidor \n";
    $stSql .= "         ON servidor_contrato_servidor.cod_contrato = max_desconto.cod_contrato \n";
    $stSql .= "       LEFT JOIN pessoal.servidor \n";
    $stSql .= "         ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor \n";
    $stSql .= "WHERE desconto_externo_previdencia.timestamp = max_desconto.timestamp \n";
    $stSql .= "  AND desconto_externo_previdencia.cod_contrato = max_desconto.cod_contrato \n";
    if ( $this->getDado( "cod_contrato" )) {
        $stSql .= " AND desconto_externo_previdencia.cod_contrato = ".$this->getDado("cod_contrato");
    }
    if ( $this->getDado( "timestamp"    )) {
        $stSql .= " AND desconto_externo_previdencia.timestamp = ".$this->getDado("timestamp");
    }
    if ( $this->getDado( "registro" )) {
        $stSql .= " AND contrato.registro = ".$this->getDado("registro");
    }

    return $stSql;
}

function recuperaParaExclusao(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaParaExclusao",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaParaExclusao()
{
    $stSql = "SELECT desconto_externo_previdencia.*  \n";
    $stSql .= "  FROM folhapagamento.desconto_externo_previdencia \n";
    $stSql .= " WHERE NOT EXISTS (SELECT 1  \n";
    $stSql .= "                     FROM folhapagamento.desconto_externo_previdencia_anulado \n";
    $stSql .= "                    WHERE desconto_externo_previdencia_anulado.cod_contrato = desconto_externo_previdencia.cod_contrato \n";
    $stSql .= "                      AND desconto_externo_previdencia_anulado.timestamp = desconto_externo_previdencia.timestamp) \n";

    return $stSql;
}

}
?>
