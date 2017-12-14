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
    * Classe de mapeamento da função fn_relatorio_situacao_restos_pagar
    * Data de Criação: 10/02/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: FEmpenhoRelatorioSituacaoRP.class.php 64417 2016-02-18 18:03:51Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class FEmpenhoRelatorioSituacaoRP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    **/
    function __construct()
    {
        parent::Persistente();
    
        $this->setTabela("empenho.fn_relatorio_situacao_restos_pagar");
    
        $this->AddCampo('cod_entidade'      ,'integer',true ,'',false,false);
        $this->AddCampo('exercicio'         ,'varchar',true ,'',false,false);
        $this->AddCampo('dt_inicial'        ,'varchar',true ,'',false,false);
        $this->AddCampo('dt_final'          ,'varchar',true ,'',false,false);
        $this->AddCampo('exercicio_empenho' ,'varchar',false,'',false,false);
        $this->AddCampo('cgm_credor'        ,'integer',false,'',false,false);
    }
    
    function montaRecuperaTodos()
    {
        $stSql  = " SELECT rp.*
                         , pre_empenho.cgm_beneficiario AS cgm_credor
                         , credor_doc.cpf_cnpj
                         , despesa.cod_recurso
                         , despesa.cod_funcao
                         , despesa.cod_subfuncao
                         , despesa.cod_estrutural
                      FROM empenho.fn_relatorio_situacao_restos_pagar   ( '".$this->getDado("exercicio")."'
                                                                        , '".$this->getDado("cod_entidade")."'
                                                                        , '".$this->getDado("dt_inicial")."'
                                                                        , '".$this->getDado("dt_final")."'
                                                                        , '".$this->getDado("exercicio_empenho")."'
                                                                        , '".$this->getDado("cgm_credor")."'
                                                                        ) AS rp
                                                                        ( cod_empenho                INTEGER,
                                                                          cod_entidade               INTEGER,
                                                                          exercicio                  VARCHAR,
                                                                          credor                     VARCHAR,
                                                                          emissao                    TEXT,
                                                                          vencimento                 TEXT,
                                                                          empenhado                  NUMERIC(14,2),
                                                                          aliquidar                  NUMERIC(14,2),
                                                                          liquidadoapagar            NUMERIC(14,2),
                                                                          anulado                    NUMERIC(14,2),
                                                                          liquidado                  NUMERIC(14,2),
                                                                          pagamento                  NUMERIC(14,2),
                                                                          empenhado_saldo            NUMERIC(14,2),
                                                                          aliquidar_saldo            NUMERIC(14,2),
                                                                          liquidadoapagar_saldo      NUMERIC(14,2)
                                                                        )
                INNER JOIN empenho.empenho
                        ON empenho.exercicio    = rp.exercicio
                       AND empenho.cod_empenho  = rp.cod_empenho
                       AND empenho.cod_entidade = rp.cod_entidade

                INNER JOIN empenho.pre_empenho
                        ON empenho.exercicio       = pre_empenho.exercicio
                       AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                INNER JOIN ( SELECT pre_empenho_despesa.exercicio
                                 , pre_empenho_despesa.cod_pre_empenho
                                 , despesa.cod_recurso
                                 , despesa.cod_funcao
                                 , despesa.cod_subfuncao
                                 , REPLACE(conta_despesa.cod_estrutural, '.', '') AS cod_estrutural
                              FROM empenho.pre_empenho_despesa
                        INNER JOIN orcamento.despesa
                                ON despesa.exercicio   = pre_empenho_despesa.exercicio
                               AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                        INNER JOIN orcamento.conta_despesa
                                ON despesa.exercicio = conta_despesa.exercicio
                               AND despesa.cod_conta = conta_despesa.cod_conta
                             UNION
                            SELECT restos_pre_empenho.exercicio
                                 , restos_pre_empenho.cod_pre_empenho
                                 , restos_pre_empenho.recurso AS cod_recurso
                                 , restos_pre_empenho.cod_funcao
                                 , restos_pre_empenho.cod_subfuncao
                                 , restos_pre_empenho.cod_estrutural
                              FROM empenho.restos_pre_empenho
                           ) AS despesa
                        ON despesa.exercicio       = pre_empenho.exercicio
                       AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       
                 LEFT JOIN ( SELECT numcgm
                                  , substr(cpf, 1, 3) || '.' ||
                                    substr(cpf, 4, 3) || '.' ||
                                    substr(cpf, 7, 3) || '-' ||
                                    substr(cpf, 10) as cpf_cnpj
                               FROM sw_cgm_pessoa_fisica
                              UNION
                             SELECT numcgm
                                  , substr(cnpj, 1, 2) || '.' || SUBSTR(cnpj, 3, 3) || '.' ||
                                    substr(cnpj, 6, 3) || '/' || SUBSTR(cnpj, 9, 4) || '-' ||
                                    substr(cnpj, 13)
                                    AS cpf_cnpj
                               FROM sw_cgm_pessoa_juridica
                           ) AS credor_doc
                        ON credor_doc.numcgm = pre_empenho.cgm_beneficiario 

        ";

        return $stSql;
    }

}
