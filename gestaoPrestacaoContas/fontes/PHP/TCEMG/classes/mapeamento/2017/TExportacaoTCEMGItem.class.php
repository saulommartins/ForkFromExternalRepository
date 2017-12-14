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
    * Classe de mapeamento da tabela Item
    * Data de Criação   : 29/01/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: TExportacaoTCEMGItem.class.php 63082 2015-07-22 19:31:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoTCEMGItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();

    $this->setTabela('almoxarifado.catalogo_item');
}

public function criaTabelaItem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
   return $this->executaRecupera("montaCriaTabelaItem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

public function montaCriaTabelaItem()
{
   $stSql = "  INSERT INTO tcemg.arquivo_item
                    SELECT AC.cod_item
                            , '".$this->getDado('exercicio')."' AS exercicio
                            , '".$this->getDado('mes')."' AS mes
                    FROM (
                        SELECT cod_item 
                        FROM licitacao.homologacao                
                        WHERE to_char(timestamp,'mmyyyy') = '".$this->getDado('mes_ano')."'
                        AND homologado = TRUE 

                        UNION

                        SELECT cod_item 
                        FROM empenho.empenho
                        INNER JOIN  empenho.item_pre_empenho 
                            ON item_pre_empenho.cod_pre_empenho     = empenho.cod_pre_empenho
                            AND item_pre_empenho.exercicio          = empenho.exercicio
                        WHERE to_char(empenho.dt_empenho,'mmyyyy') = '".$this->getDado('mes_ano')."'

                        UNION
                
                        SELECT cod_item 
                        FROM compras.homologacao
                        WHERE TO_CHAR(timestamp,'mmyyyy') = '".$this->getDado('mes_ano')."'
                    ) as itens

                    INNER JOIN almoxarifado.catalogo_item AS AC
                            ON AC.cod_item = itens.cod_item
                  
                    LEFT JOIN tcemg.arquivo_item
                           ON arquivo_item.cod_item = AC.cod_item                    
        
                    WHERE AC.ativo = TRUE 
                      AND arquivo_item.cod_item IS NULL ";
   return $stSql;
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT 10 AS tipoRegistro
                    , AC.cod_item AS codItem
                    , remove_acentos(SUBSTR(RTRIM(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(AC.descricao,Chr(8211), '-'),Chr(9), ' '),Chr(39), ''), Chr(59) , ''), Chr(34), '')), 0, 240)) || '-' || AC.cod_item::VARCHAR AS dscItem
                    , remove_acentos(AU.nom_unidade) AS unidadeMedida
                    , 1 AS tipoCadastro
                    , '':: TEXT AS justificativaAlteracao
                FROM (
                        SELECT cod_item 
                        FROM licitacao.homologacao                
                        WHERE to_char(timestamp,'mmyyyy') = '".$this->getDado('mes_ano')."'
                        AND homologado = TRUE 

                        UNION

                        SELECT cod_item 
                        FROM empenho.empenho
                        INNER JOIN  empenho.item_pre_empenho 
                            ON item_pre_empenho.cod_pre_empenho     = empenho.cod_pre_empenho
                            AND item_pre_empenho.exercicio          = empenho.exercicio
                        WHERE to_char(empenho.dt_empenho,'mmyyyy') = '".$this->getDado('mes_ano')."'

                        UNION

                        SELECT cod_item 
                        FROM compras.homologacao
                        WHERE TO_CHAR(timestamp,'mmyyyy') = '".$this->getDado('mes_ano')."'
                ) as itens

                INNER JOIN almoxarifado.catalogo_item AS AC
                    ON AC.cod_item = itens.cod_item

                LEFT JOIN administracao.unidade_medida AS AU
                     ON AU.cod_unidade  = AC.cod_unidade
                    AND AU.cod_grandeza = AC.cod_grandeza

                INNER JOIN tcemg.arquivo_item
                     ON arquivo_item.cod_item = AC.cod_item
                    AND arquivo_item.mes = '".$this->getDado('mes')."'
                    AND arquivo_item.exercicio = '".$this->getDado('exercicio')."'

                WHERE AC.ativo = TRUE 
        ";

    return $stSql;
}

public function __destruct(){}

}
