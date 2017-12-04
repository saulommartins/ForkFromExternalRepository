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

/*
    * Classe de mapeamento da tabela tcemg.nota_fiscal
    * Data de Criação   : 05/02/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: TTCEMGNotaFiscal.class.php 62223 2015-04-09 14:08:10Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGNotaFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTCEMGNotaFiscal()
{
    parent::Persistente();
    $this->setTabela("tcemg.nota_fiscal");

    $this->setCampoCod('cod_nota');
    $this->setComplementoChave('exercicio , cod_entidade');
    $this->AddCampo( 'cod_nota'                 , 'integer' , true  , ''     , true  , false  );
    $this->AddCampo( 'exercicio'                , 'varchar' , true  , '4'    , true  , false  );
    $this->AddCampo( 'cod_entidade'             , 'integer' , true  , ''     , true  , false  );
    $this->AddCampo( 'nro_nota'                 , 'varchar' , false , '20'   , false , false  );
    $this->AddCampo( 'nro_serie'                , 'varchar' , false , '8'    , false , false  );
    $this->AddCampo( 'aidf'                     , 'varchar' , false , '15'   , false , false  );
    $this->AddCampo( 'data_emissao'             , 'date'    , true  , ''     , false , false  );
    $this->AddCampo( 'inscricao_municipal'      , 'varchar' , false , '30'   , false , false  );
    $this->AddCampo( 'inscricao_estadual'       , 'varchar' , false , '30'   , false , false  );
    $this->AddCampo( 'cod_tipo'                 , 'integer' , true  , ''     , true  , true   );
    $this->AddCampo( 'chave_acesso'             , 'numeric' , false , '44,0' , false , false  );
    $this->AddCampo( 'chave_acesso_municipal'   , 'varchar' , false , '60'   , false , false  );
    $this->AddCampo( 'vl_desconto'              , 'numeric' , true  , '14,2' , false , false  );
    $this->AddCampo( 'vl_total'                 , 'numeric' , true  , '14,2' , false , false  );
    $this->AddCampo( 'vl_total_liquido'         , 'numeric' , true  , '14,2' , false , false  );

}

function recuperaNotasFiscais(&$rsRecordSet, $stFiltro, $stOrdem="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNotasFiscais().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaNotasFiscais()
{
    $stSql  = " SELECT DISTINCT ON (NF.cod_nota, NF.exercicio)  NF.cod_nota
                    , NF.nro_nota
                    , NF.exercicio
                    , NF.nro_serie
                    , NF.chave_acesso
                    , to_char(data_emissao, 'dd/mm/yyyy') as data_emissao
                    , NF.vl_total_liquido AS vl_nota
                    , NF.inscricao_municipal
                    , NF.inscricao_estadual
                    , NF.cod_tipo
                    , CASE WHEN NF.chave_acesso::VARCHAR IS NOT NULL 
                           THEN NF.chave_acesso::VARCHAR
                           ELSE NF.chave_acesso_municipal
                     END AS chave_acesso                    
                    , NF.cod_entidade
                    , NF.aidf
                    , COALESCE(NF.vl_total, 0.00) AS vl_total
                    , COALESCE(NF.vl_desconto, 0.00) AS vl_desconto
                    , COALESCE(NF.vl_total_liquido, 0.00) AS vl_total_liquido
                    FROM tcemg.nota_fiscal AS NF
                    LEFT JOIN tcemg.nota_fiscal_empenho_liquidacao AS NFEL
                        ON(NFEL.cod_nota = NF.cod_nota)
                        AND (NFEL.exercicio = NF.exercicio)
                    LEFT JOIN tcemg.nota_fiscal_empenho AS NFE
                        ON(NFE.cod_nota = NF.cod_nota)
                        AND (NFE.exercicio = NF.exercicio)
    ";

    return $stSql;
}

function recuperaNTF10(&$rsRecordSet, $stFiltro="", $stOrdem="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNTF10().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaNTF10()
{
    $stSql  = " SELECT DISTINCT ON (NF.nro_nota::NUMERIC, NF.exercicio)  
            10 AS tiporegistro
            , RPAD((NF.cod_nota||''||NF.nro_nota||''||to_char(data_emissao, 'ddmmyyyy')), 15, '0') AS codnotafiscal
            
            , (SELECT valor::INTEGER
                    FROM administracao.configuracao_entidade
                    WHERE exercicio=NF.exercicio
                    AND parametro='tcemg_codigo_orgao_entidade_sicom'
                    AND cod_entidade=NF.cod_entidade)
              AS codorgao ";

    //Alteração para 2015
    //Registro 10
    //Para exercicios anteriores a 2015 existe uma verificação no campo nfNumero : Não informar este campo caso a nota fiscal seja eletrônica e siga o padrão ajuste SINIEF 07/05.
    //Retirar essa verificação, deve demonstrar esse campo para todas as notas fiscais
    if ( Sessao::getExercicio() >= '2015' ) {
        $stSql .= "\n,NF.nro_nota AS nfnumero ";
    }else{
        $stSql .= "\n, CASE WHEN tipo_nota_fiscal.cod_tipo != 4 AND tipo_nota_fiscal.cod_tipo != 1 THEN 
                                NF.nro_nota 
                            ELSE        
                                ' '
                       END AS nfnumero ";
    }

    $stSql .="\n, CASE WHEN tipo_nota_fiscal.cod_tipo != 4 AND tipo_nota_fiscal.cod_tipo != 1 THEN
                    NF.nro_serie 
                ELSE
                    ' '
              END AS nfserie
            
            , CASE WHEN CGMPJ_empenho.cnpj!='' THEN
                2
              ELSE
                CASE WHEN CGMPF_empenho.cpf!='' THEN
                    1
                ELSE
                    3
                END
              END AS tipodocumento

            , CASE WHEN CGMPJ_empenho.cnpj!='' THEN
                CGMPJ_empenho.cnpj
              ELSE
                CASE WHEN CGMPF_empenho.cpf!='' THEN
                    CGMPF_empenho.cpf
                ELSE
                    ''
                END
              END AS nrodocumento
              
              
            , NF.inscricao_estadual AS nroinscestadual
            , NF.inscricao_municipal  AS nroinscmunicipal
            , CGMMUN.nom_municipio as nomemunicipio
            , (select cep 
                    from sw_cep_logradouro
                    where cod_logradouro=(select cod_logradouro
                                                from sw_logradouro
                                                where cod_municipio=CGM.cod_municipio
                                                and cod_uf=CGM.cod_uf
                                                order by cod_logradouro desc limit 1) )
              AS cepmunicipio
            , (SELECT sw_uf.sigla_uf 
                    FROM empenho.empenho AS EE
                    LEFT JOIN empenho.pre_empenho AS EPE
                        ON EPE.exercicio=EE.exercicio
                        AND EPE.cod_pre_empenho=EE.cod_pre_empenho
                    LEFT JOIN sw_cgm
                        ON sw_cgm.numcgm=EPE.cgm_beneficiario
                    LEFT JOIN sw_uf
                        ON sw_uf.cod_uf=sw_cgm.cod_uf
                        AND sw_uf.cod_pais=sw_cgm.cod_pais
                    WHERE (EE.exercicio=NFEL.exercicio_empenho
                        AND EE.cod_empenho=NFEL.cod_empenho
                        AND EE.cod_entidade=NFEL.cod_entidade)
                    OR (EE.exercicio=NFE.exercicio_empenho
                        AND EE.cod_empenho=NFE.cod_empenho
                        AND EE.cod_entidade=NFE.cod_entidade))
              AS ufcredor
            , CASE WHEN NF.cod_tipo = 1 THEN 1
                   WHEN NF.cod_tipo = 4 THEN 1
                   WHEN NF.cod_tipo = 2 THEN 2
                   WHEN NF.cod_tipo = 3 THEN 3
                   ELSE 0
              END AS notafiscaleletronica
            , NF.chave_acesso AS chaveacesso
            , NF.chave_acesso_municipal AS chaveacessomunicipal
            , NF.aidf AS nfaidf
            , to_char(data_emissao, 'ddmmyyyy') as dtemissaonf
            , CASE WHEN ENL.dt_vencimento < NF.data_emissao OR EE.dt_vencimento < NF.data_emissao
                   THEN to_char(data_emissao, 'ddmmyyyy')
                   ELSE CASE WHEN ENL.dt_vencimento IS NOT NULL
                             THEN to_char(ENL.dt_vencimento, 'ddmmyyyy')
                             ELSE to_char(EE.dt_vencimento, 'ddmmyyyy')
                         END
                    END AS dtvencimentonf
            , REPLACE(NF.vl_total::VARCHAR,'.',',') as nfvalortotal
            , REPLACE(NF.vl_desconto::VARCHAR,'.',',') as nfvalordesconto
            , REPLACE(NF.vl_total_liquido::VARCHAR,'.',',') as nfvalorliquido
            , NF.cod_nota
            , NF.exercicio
            , NF.cod_entidade
            FROM tcemg.nota_fiscal AS NF
            JOIN tcemg.tipo_nota_fiscal
                ON tipo_nota_fiscal.cod_tipo = NF.cod_tipo
            LEFT JOIN tcemg.nota_fiscal_empenho_liquidacao AS NFEL
                ON  NFEL.cod_nota = NF.cod_nota
                AND NFEL.exercicio = NF.exercicio
                AND NFEL.cod_entidade = NF.cod_entidade
            LEFT JOIN tcemg.nota_fiscal_empenho AS NFE
                ON  NFE.cod_nota = NF.cod_nota
                AND NFE.exercicio = NF.exercicio
                AND NFE.cod_entidade = NF.cod_entidade
            LEFT JOIN orcamento.entidade AS OE
                ON  OE.cod_entidade=NF.cod_entidade
                AND OE.exercicio=NF.exercicio
            LEFT JOIN sw_cgm AS CGM
                ON  CGM.numcgm = OE.numcgm
            LEFT JOIN sw_cgm_pessoa_juridica AS CGMPJ
                ON  CGMPJ.numcgm = OE.numcgm
            LEFT JOIN sw_cgm_pessoa_fisica AS CGMPF
                ON  CGMPF.numcgm = OE.numcgm
            LEFT JOIN sw_municipio AS CGMMUN
                ON  CGMMUN.cod_municipio = CGM.cod_municipio
                AND CGMMUN.cod_uf = CGM.cod_uf
            LEFT JOIN empenho.nota_liquidacao AS ENL
                ON  ENL.exercicio=NFEL.exercicio_liquidacao
                AND ENL.cod_nota=NFEL.cod_nota_liquidacao
                AND ENL.cod_entidade=NFEL.cod_entidade
                
            LEFT JOIN empenho.empenho AS EE
                ON  EE.exercicio=NFEL.exercicio_empenho
                AND EE.cod_empenho=NFEL.cod_empenho
                AND EE.cod_entidade=NFEL.cod_entidade
                
            LEFT JOIN empenho.pre_empenho AS EP
                ON  EP.exercicio        = EE.exercicio
                AND EP.cod_pre_empenho  = EE.cod_pre_empenho

            LEFT JOIN sw_cgm AS CGM_empenho
                ON  CGM_empenho.numcgm = EP.cgm_beneficiario
                
            LEFT JOIN sw_cgm_pessoa_juridica AS CGMPJ_empenho
                ON  CGMPJ_empenho.numcgm = CGM_empenho.numcgm

            LEFT JOIN sw_cgm_pessoa_fisica AS CGMPF_empenho
                ON  CGMPF_empenho.numcgm = CGM_empenho.numcgm

            WHERE NF.exercicio='".$this->getDado('exercicio')."'
                AND NF.cod_entidade IN (".$this->getDado('cod_entidade').")
                AND (NF.nro_nota IS NOT NULL OR TRIM(NF.nro_nota) <> '' )
                AND ENL.dt_liquidacao BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                ";
                
    return $stSql;
}

function recuperaNTF20(&$rsRecordSet, $stFiltro="", $stOrdem="", $boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNTF20().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaNTF20()
{
    $stSql  = " SELECT 20 AS tiporegistro
                , '".$this->getDado('codnotafiscal')."'::TEXT AS codnotafiscal
                , nota_fiscal.nro_nota AS nfnumero
                , CASE WHEN tipo_nota_fiscal.cod_tipo != 4 AND tipo_nota_fiscal.cod_tipo != 1 THEN
                                nota_fiscal.nro_serie 
                        ELSE
                                ' '
                  END AS nfserie
                , CASE WHEN sw_cgm_pessoa_juridica.cnpj !='' THEN
                                2
                        ELSE
                        CASE WHEN sw_cgm_pessoa_fisica.cpf !='' THEN
                                1
                        ELSE
                                3
                        END
                 END AS tipodocumento
                , CASE WHEN sw_cgm_pessoa_juridica.cnpj !='' THEN
                                sw_cgm_pessoa_juridica.cnpj
                        ELSE
                        CASE WHEN sw_cgm_pessoa_fisica.cpf !='' THEN
                                sw_cgm_pessoa_fisica.cpf
                        ELSE
                                ''
                        END
                 END AS nrodocumento
                , nota_fiscal.chave_acesso AS chaveacesso
                , to_char(data_emissao, 'ddmmyyyy') as dtemissaonf
                , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       THEN CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
                                 THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                 ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                             END
                       ELSE LPAD((LPAD(''||OD.num_orgao,2, '0')||LPAD(''||OD.num_unidade,2, '0')), 5, '0')
                   END AS codunidadesub
                , to_char(EE.dt_empenho, 'ddmmyyyy') AS dtempenho
                , NFEL.cod_empenho AS nroempenho
                , to_char(ENL.dt_liquidacao, 'ddmmyyyy') AS dtliquidacao                                
                , TCEMG.numero_nota_liquidacao('2014',
                                                                 EE.cod_entidade,
                                                                 ENL.cod_nota,
                                                                 ENL.exercicio_empenho,
                                                                 EE.cod_empenho
                                                                ) AS nroliquidacao
                FROM tcemg.nota_fiscal_empenho_liquidacao AS NFEL
                LEFT JOIN tcemg.nota_fiscal
                     ON nota_fiscal.cod_nota        = NFEL.cod_nota
                    AND nota_fiscal.exercicio       = NFEL.exercicio
                    AND nota_fiscal.cod_entidade    = NFEL.cod_entidade                
                LEFT JOIN tcemg.tipo_nota_fiscal
                     ON tipo_nota_fiscal.cod_tipo = nota_fiscal.cod_tipo
                LEFT JOIN orcamento.entidade
                     ON  entidade.cod_entidade = nota_fiscal.cod_entidade
                    AND entidade.exercicio    = nota_fiscal.exercicio
                LEFT JOIN sw_cgm_pessoa_juridica
                     ON  sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                LEFT JOIN sw_cgm_pessoa_fisica
                     ON  sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                LEFT JOIN empenho.empenho AS EE
                     ON  EE.cod_empenho = NFEL.cod_empenho
                    AND EE.exercicio    = NFEL.exercicio_empenho
                    AND EE.cod_entidade = NFEL.cod_entidade
                LEFT JOIN empenho.nota_liquidacao AS ENL
                     ON  ENL.cod_nota    = NFEL.cod_nota_liquidacao
                    AND ENL.exercicio    = NFEL.exercicio_liquidacao
                    AND ENL.cod_entidade = NFEL.cod_entidade
                LEFT JOIN empenho.pre_empenho_despesa AS EPED
                     ON  EPED.cod_pre_empenho = EE.cod_pre_empenho
                    AND EPED.exercicio        = EE.exercicio
                LEFT JOIN orcamento.despesa AS OD
                     ON  OD.exercicio    = EPED.exercicio
                    AND OD.cod_despesa   = EPED.cod_despesa
                LEFT JOIN empenho.pre_empenho
                     ON pre_empenho.cod_pre_empenho = ee.cod_pre_empenho
                    AND pre_empenho.exercicio = ee.exercicio
                LEFT JOIN empenho.restos_pre_empenho
                     ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                    AND pre_empenho.exercicio = restos_pre_empenho.exercicio 
               LEFT JOIN tcemg.uniorcam
                      ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
					 AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao
                     AND uniorcam.exercicio   = restos_pre_empenho.exercicio
                     AND uniorcam.num_orgao_atual IS NOT NULL

                WHERE NFEL.exercicio    ='".$this->getDado('exercicio')."'
                AND NFEL.cod_nota       =".$this->getDado('cod_nota')."
                AND NFEL.cod_entidade   =".$this->getDado('cod_entidade')."
                AND ENL.dt_liquidacao   BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) 
                                        AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                ";

    return $stSql;
}

public function __destruct(){}

}

?>
