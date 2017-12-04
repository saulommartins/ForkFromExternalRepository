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
    * Classe de mapeamento da funcao orcamento.fn_mapa_recursos
    * Data de Criação: 18/11/2008

    * @author Analista: Tonismar Bernanrdo
    * @author Desenvolvedor: André Machado

    $Id:  $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FMapaRecursos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FMapaRecursos()
{
    parent::Persistente();
    $this->setTabela('orcamento.fn_mapa_recursos');

    $this->AddCampo('exercicio'   ,'varchar',false,'' ,false,false);
    $this->AddCampo('data_final'   ,'date',false,'' ,false,false);
    $this->AddCampo('cod_entidade'   ,'varchar',false,'' ,false,false);
    $this->AddCampo('cod_recurso_ini'   ,'integer',false,'' ,false,false);
    $this->AddCampo('cod_recurso_fim'   ,'integer',false,'' ,false,false);
}

function montaExecutaFuncao()
{
    $stSql = "
        SELECT * from ".$this->getTabela()."( '".$this->getDado('exercicio')."'
                                      ,'".$this->getDado('data_final')."'
                                      ,'".$this->getDado('cod_entidade')."'
                                      ,".$this->getDado('cod_recurso_ini')."
                                      ,".$this->getDado('cod_recurso_fim')."
                                     ) AS retorno(
    entidade                         VARCHAR,
    cod_recurso                      INTEGER,
    nom_recurso                      VARCHAR,
    saldo_inicial                    NUMERIC(14,2),
    vl_arrec_orcamentaria            NUMERIC(14,2),
    vl_est_arrec_orcamentaria        NUMERIC(14,2),
    vl_arrec_extra_orcamentaria      NUMERIC(14,2),
    vl_est_arrec_extra_orcamentaria  NUMERIC(14,2),
    vl_pag_orcamentario              NUMERIC(14,2),
    vl_est_pag_orcamentario          NUMERIC(14,2),
    vl_pag_extra_orcamentaria        NUMERIC(14,2),
    vl_est_pag_extra_orcamentaria    NUMERIC(14,2),
    saldo_atual                      NUMERIC(14,2)
)";

    return $stSql;
}

/**
    * Executa funcao EmpenhoEmissao no banco de dados a partir do comando SQL montado no método montaExecutaFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function executaFuncao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaExecutaFuncao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
?>
