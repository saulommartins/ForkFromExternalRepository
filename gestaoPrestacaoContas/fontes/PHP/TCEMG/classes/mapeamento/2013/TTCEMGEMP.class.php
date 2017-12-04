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
    * Classe de mapeamento da tabela TTCEMG
    * Data de Criação: 14/03/2014

    * @author Analista: Sergio Luiz dos Santos
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGEMP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGEMP()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosEMP10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosEMP10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosEMP10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosEMP10()
    {
        $stSql  = " SELECT DISTINCT ON(EE.cod_empenho)
                    10 AS tiporegistro
                    ,	(SELECT valor
                        FROM administracao.configuracao_entidade
                        WHERE exercicio=EE.exercicio
                        AND parametro='tcemg_codigo_orgao_entidade_sicom'
                        AND cod_entidade=EE.cod_entidade)
                    AS codOrgao
                    , LPAD((LPAD(''||OD.num_orgao,2, '0')||LPAD(''||OD.num_unidade,2, '0')), 5, '0') AS codunidadesub
                    , (LPAD(''||OD.cod_funcao,2, '0')) AS codfuncao
                    , LPAD(OD.cod_subfuncao::varchar,3,'0') AS codsubfuncao
                    ,	(LPAD(''||(SELECT num_programa FROM ppa.programa
                        WHERE cod_programa=OP.cod_programa AND ativo=true LIMIT 1),4, '0'))::VARCHAR
                    AS codprograma
                    , (LPAD(''||acao.num_acao,4, '0')) AS idacao
                    , ''::TEXT AS idsubacao
                    , (LPAD(''||REPLACE(OCD.cod_estrutural, '.', ''),6, '')) AS naturezadespesa
                    --, SUBSTR(REPLACE(OCD.cod_estrutural, '.', ''),7,2) AS subelemento
                    
                    , CASE WHEN (LPAD(''||REPLACE(OCD.cod_estrutural, '.', ''),6, '')) = '339031' OR
                                (LPAD(''||REPLACE(OCD.cod_estrutural, '.', ''),6, '')) = '329091' OR
                                (LPAD(''||REPLACE(OCD.cod_estrutural, '.', ''),6, '')) = '339049' OR
                                (LPAD(''||REPLACE(OCD.cod_estrutural, '.', ''),6, '')) = '339041'
                           THEN '00'
                           ELSE
                               SUBSTR(REPLACE(OCD.cod_estrutural, '.', ''),7,2)	
                           END AS subelemento
                    
                    , EE.cod_empenho AS nroempenho
                    , to_char(EE.dt_empenho, 'ddmmyyyy') AS dtempenho
                    ,   CASE WHEN ETE.nom_tipo = 'Ordinário' THEN
                            1
                        WHEN ETE.nom_tipo = 'Estimado' THEN
                            2
                        WHEN ETE.nom_tipo = 'Global' THEN
                            3
                        ELSE
                            0
                        END
                    AS modalidadeempenho
                    ,   CASE WHEN (SUBSTR(REPLACE(OCD.cod_estrutural, '.', ''),1,2)::INTEGER)=46 THEN
                            02
                        ELSE
                            01
                        END
                    AS tpempenho
                    , REPLACE((	SELECT SUM(vl_total)::TEXT
                                  FROM empenho.item_pre_empenho
                                  WHERE exercicio=EPE.exercicio
                                  AND cod_pre_empenho=EE.cod_pre_empenho), '.', ',')
                    AS vlbruto
                    , regexp_replace(remove_acentos(REPLACE((REPLACE(REPLACE(EPE.descricao,Chr(39), ''), Chr(10), '')), Chr(13), ' ')),'[º|°]', '', 'gi') AS especificacaoempenho
                    ,	CASE WHEN TCE.cod_contrato IS NOT NULL THEN
                            CASE WHEN TC.cod_modalidade_licitacao = 5 OR TC.cod_modalidade_licitacao = 6 THEN
                                3
                            ELSE
                                1
                            END
                        ELSE
                            2
                        END
                    AS despdeccontrato
                    ,   CASE WHEN TC.cod_modalidade_licitacao = 5 OR TC.cod_modalidade_licitacao = 6 THEN
                            (SELECT valor
                            FROM administracao.configuracao_entidade
                            WHERE exercicio=TC.exercicio
                            AND parametro='tcemg_codigo_orgao_entidade_sicom'
                            AND cod_entidade=TC.cod_entidade_modalidade)
                        ELSE
                            ''
                        END
                    AS codorgaorespcontrato
                    ,   CASE WHEN TCE.cod_contrato IS NOT NULL THEN
                            LPAD((LPAD(''||TC.num_orgao,2, '0')||LPAD(''||TC.num_unidade,2, '0')), 5, '0')
                        END
                    AS codunidadesubrespcontrato
                    ,   CASE WHEN TCE.cod_contrato IS NOT NULL THEN
                            (TC.exercicio||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.nro_contrato,8, '0')))
                        END
                    AS nrocontrato
                    ,   CASE WHEN TCE.cod_contrato IS NOT NULL THEN
                            to_char(TC.data_assinatura, 'ddmmyyyy')
                        END
                    AS dataassinaturacontrato
                    ,   CASE WHEN TCE.cod_contrato IS NOT NULL THEN
                            (select nro_aditivo from tcemg.contrato_aditivo
                            where contrato_aditivo.cod_contrato=TCE.cod_contrato
                            and contrato_aditivo.exercicio=TCE.exercicio
                            and contrato_aditivo.cod_entidade=TCE.cod_entidade order by nro_aditivo desc limit 1)
                        END
                    AS nrosequencialtermoaditivo
                    ,   CASE WHEN TCEMP.cod_convenio IS NOT NULL THEN
                            1
                        ELSE
                            2
                        END
                    AS despdecconvenio
                    ,   CASE WHEN TCEMP.cod_convenio IS NOT NULL THEN
                            (TCONV.exercicio||(LPAD(''||TCONV.cod_entidade,2, '0'))||(LPAD(''||TCONV.nro_convenio,24, '0')))
                        END
                    AS nroconvenio
                    ,   CASE WHEN TCEMP.cod_convenio IS NOT NULL THEN
                            to_char(TCONV.data_assinatura, 'ddmmyyyy')
                        END
                    AS dataassinaturaconvenio
                    
                    
                    ,   CASE WHEN E_AEVALOR.valor::INTEGER = 5 THEN
                                1
                        ELSE
                            CASE WHEN C_CD.cod_compra_direta IS NOT NULL THEN
                                    1
                                 WHEN L_LIC.cod_licitacao IS NOT NULL THEN
                                    CASE WHEN L_LIC.cod_modalidade = 8 OR L_LIC.cod_modalidade = 9 THEN
                                            3
                                         WHEN L_LIC.cod_modalidade = 11 THEN
                                            4
                                    ELSE
                                            2
                                    END
                                    
                                 WHEN E_AEVALOR.valor IS NOT NULL THEN
                                    CASE WHEN E_AEVALOR.valor::INTEGER = 5 OR E_AEVALOR.valor::INTEGER = 7 OR E_AEVALOR.valor::INTEGER = 15 THEN
                                            1
                                         WHEN E_AEVALOR.valor::INTEGER = 6 OR E_AEVALOR.valor::INTEGER = 13 THEN
                                            3
                                         WHEN E_AEVALOR.valor::INTEGER = 14 THEN
                                            4
                                         WHEN E_AEVALOR.valor::INTEGER = 3 THEN
                                            2
                                    ELSE
                                            2
                                    END
                            END
                        END
                    AS despdeclicitacao
                    
                    
                    , '' AS codorgaoresplicit
                  , CASE WHEN E_AEVALOR.valor::INTEGER = 5
                           THEN ' '
                           ELSE CASE WHEN C_CD.cod_compra_direta IS NULL
                                THEN ' '
                                ELSE CASE WHEN L_LIC.cod_licitacao IS NOT NULL
                                          THEN LPAD((LPAD(''||L_LIC.num_orgao,2, '0')||LPAD(''||L_LIC.num_unidade,2, '0')), 5, '0')
                                          WHEN E_AEVALOR.valor::INTEGER = 14 OR E_AEVALOR.valor::INTEGER = 4 
                                          THEN LPAD((LPAD(''||RegPrecoOrgao.num_orgao,2, '0')||LPAD(''||RegPrecoOrgao.num_unidade,2, '0')), 5, '0')
                                     END
                                END
                       END AS codUnidadeSubRespLicit
                       
                       
                   , 	CASE WHEN C_CD.cod_compra_direta IS NULL THEN
                            CASE WHEN E_AEVALOR.valor::INTEGER = 5 THEN
                                    ' '
                            ELSE
                                    CASE WHEN L_LIC.cod_licitacao IS NOT NULL THEN
                                                config_licitacao.num_licitacao
                                         WHEN E_AEVALOR.valor::INTEGER = 14 OR E_AEVALOR.valor::INTEGER = 4 THEN
                                                CASE WHEN RegPreco.exercicio_licitacao IS NOT NULL THEN
                                                    RegPreco.exercicio_licitacao::varchar||LPAD(''||RegPreco.cod_entidade::varchar,2, '0')||LPAD(''||RegPreco.codigo_modalidade_licitacao::varchar,2, '0')||LPAD(''||RegPreco.numero_processo_licitacao ::varchar,4, '0')
                                                ELSE
                                                    arquivo_emp.exercicio_licitacao::varchar||LPAD(''||arquivo_emp.cod_entidade::varchar,2, '0')||LPAD(''||arquivo_emp.cod_modalidade::varchar,2, '0')||LPAD(''||arquivo_emp.cod_licitacao ::varchar,4, '0')
                                                END
                                        ELSE
                                                arquivo_emp.exercicio_licitacao::varchar||LPAD(''||arquivo_emp.cod_entidade::varchar,2, '0')||LPAD(''||arquivo_emp.cod_modalidade::varchar,2, '0')||LPAD(''||arquivo_emp.cod_licitacao ::varchar,4, '0')
                                    END
                                END
                            END
                        AS nroProcessoLicitatorio    

               
                    , 	CASE WHEN C_CD.cod_compra_direta IS NULL THEN
                            CASE WHEN E_AEVALOR.valor::INTEGER = 5 THEN
                                    ' '
                            ELSE
                                    CASE WHEN L_LIC.cod_licitacao IS NOT NULL THEN
                                            config_licitacao.exercicio_licitacao
                                         WHEN E_AEVALOR.valor::INTEGER = 14 OR E_AEVALOR.valor::INTEGER = 4 THEN
                                            CASE WHEN RegPreco.exercicio_licitacao IS NOT NULL THEN
                                                RegPreco.exercicio_licitacao
                                            ELSE
                                                arquivo_emp.exercicio_licitacao
                                            END
                                     END
                                END
                            END
                        AS exercicioProcessoLicitatorio
                    
                    , 	CASE WHEN C_CD.cod_compra_direta IS NULL THEN
                            CASE WHEN E_AEVALOR.valor::INTEGER = 5 THEN
                                    ' '
                            ELSE
                                CASE WHEN L_LIC.cod_modalidade = 8 OR E_AEVALOR.valor::INTEGER = 15 THEN
                                        '1'
                                     WHEN L_LIC.cod_modalidade = 9 OR E_AEVALOR.valor::INTEGER = 6 THEN
					'2'
				ELSE
					''
                                END
                            END
			ELSE
			    ''
			END
                    AS tipoProcesso
                    ,   CASE WHEN uniorcam.cgm_ordenador IS NOT NULL THEN
                            (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE numcgm=uniorcam.cgm_ordenador)
                        ELSE
                            ''
                        END
                    AS cpfOrdenador
                    ,   CASE WHEN C_CD.cod_compra_direta IS NULL THEN
			      CASE  WHEN tipo_objeto.cod_tipo_objeto = 1 THEN
                                        CASE  WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 15000) THEN 2
                                              ELSE 99
				         END
                                    WHEN tipo_objeto.cod_tipo_objeto = 2 THEN
                                        CASE WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 8000) THEN 1
					     ELSE 99
					END
                                    WHEN tipo_objeto.cod_tipo_objeto = 3 THEN 3
                                    WHEN tipo_objeto.cod_tipo_objeto = 4 THEN 3
                              END
                        END AS natureza_objeto
                    
                    FROM empenho.empenho AS EE
                    
                    LEFT JOIN empenho.empenho_anulado AS EEANUL
                    ON EEANUL.exercicio=EE.exercicio
                    AND EEANUL.cod_entidade=EE.cod_entidade
                    AND EEANUL.cod_empenho=EE.cod_empenho
                    
                    INNER JOIN empenho.pre_empenho AS EPE
                    ON EPE.cod_pre_empenho=EE.cod_pre_empenho
                    AND EPE.exercicio=EE.exercicio
                    
                    INNER JOIN empenho.tipo_empenho AS ETE
                    ON ETE.cod_tipo=EPE.cod_tipo
                    
                    INNER JOIN empenho.pre_empenho_despesa AS EPED
                    ON EPED.cod_pre_empenho=EPE.cod_pre_empenho
                    AND EPED.exercicio=EPE.exercicio
                    
                    INNER JOIN orcamento.despesa AS OD
                    ON OD.exercicio=EPED.exercicio AND OD.cod_despesa=EPED.cod_despesa
                    
                    INNER JOIN orcamento.conta_despesa AS OCD
                    ON OCD.exercicio=EPED.exercicio
                    AND OCD.cod_conta=EPED.cod_conta
                    
                    INNER JOIN orcamento.programa AS OP
                    ON OP.cod_programa=OD.cod_programa
                    AND OP.exercicio=OD.exercicio
                    
                    INNER JOIN orcamento.despesa_acao AS ODA
                    ON ODA.cod_despesa=OD.cod_despesa
                    AND ODA.exercicio_despesa=OD.exercicio
                    
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.cod_despesa = OD.cod_despesa
                     AND despesa_acao.exercicio_despesa = OD.exercicio
                    
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    
                    LEFT JOIN tcemg.contrato_empenho AS TCE
                    ON TCE.exercicio=EE.exercicio
                    AND TCE.cod_entidade=EE.cod_entidade
                    AND TCE.cod_empenho=EE.cod_empenho
                    
                    LEFT JOIN tcemg.contrato AS TC
                    ON TCE.exercicio=TC.exercicio
                    AND TCE.cod_entidade=TC.cod_entidade
                    AND TCE.cod_contrato=TC.cod_contrato
                    
                    LEFT JOIN tcemg.convenio_empenho AS TCEMP
                    ON TCEMP.exercicio=EE.exercicio
                    AND TCEMP.cod_entidade=EE.cod_entidade
                    AND TCEMP.cod_empenho=EE.cod_empenho
                    
                    LEFT JOIN tcemg.convenio AS TCONV
                    ON TCEMP.exercicio=TCONV.exercicio
                    AND TCEMP.cod_entidade=TCONV.cod_entidade
                    AND TCEMP.cod_convenio=TCONV.cod_convenio
                    
                    LEFT JOIN tcemg.uniorcam
                    ON uniorcam.num_unidade=OD.num_unidade
                    AND uniorcam.num_orgao=OD.num_orgao
                    AND uniorcam.exercicio=OD.exercicio
                    
                    LEFT JOIN empenho.empenho_autorizacao AS E_EA
                    ON E_EA.exercicio=EE.exercicio
                    AND E_EA.cod_entidade=EE.cod_entidade
                    AND E_EA.cod_empenho=EE.cod_empenho
                    
                    LEFT JOIN empenho.autorizacao_empenho AS E_AE
                    ON E_AE.exercicio=E_EA.exercicio
                    AND E_AE.cod_entidade=E_EA.cod_entidade
                    AND E_AE.cod_autorizacao=E_EA.cod_autorizacao
                    
                    LEFT JOIN empenho.autorizacao_anulada AS E_AANUL
                    ON E_AANUL.cod_autorizacao=E_AE.cod_autorizacao
                    AND E_AANUL.exercicio=E_AE.exercicio
                    AND E_AANUL.cod_entidade=E_AE.cod_entidade
                    
                    LEFT JOIN empenho.item_pre_empenho_julgamento AS E_IPEJ
                    ON E_IPEJ.exercicio=EPE.exercicio
                    AND E_IPEJ.cod_pre_empenho=EPE.cod_pre_empenho
                    AND E_IPEJ.num_item=(SELECT E_IPEJ.num_item
                            FROM empenho.item_pre_empenho_julgamento AS E_IPEJ
                            WHERE E_IPEJ.exercicio=EPE.exercicio
                            AND E_IPEJ.cod_pre_empenho=EPE.cod_pre_empenho LIMIT 1)
                    
                    LEFT JOIN compras.julgamento_item AS C_JI
                    ON C_JI.cod_cotacao=E_IPEJ.cod_cotacao
                    AND C_JI.exercicio=E_IPEJ.exercicio
                    AND C_JI.cod_item=E_IPEJ.cod_item
                    
                    LEFT JOIN compras.mapa_cotacao AS C_MC
                    ON C_MC.cod_cotacao=E_IPEJ.cod_cotacao
                    AND C_MC.exercicio_cotacao=E_IPEJ.exercicio
                    
                    LEFT JOIN compras.compra_direta AS C_CD
                    ON C_CD.exercicio_mapa=C_MC.exercicio_mapa
                    AND C_CD.cod_mapa=C_MC.cod_mapa
                    
                    LEFT JOIN compras.compra_direta_anulacao as C_CDA
                    ON C_CDA.cod_compra_direta =C_CD.cod_compra_direta
                    AND C_CDA.cod_modalidade=C_CD.cod_modalidade
                    
                    LEFT JOIN licitacao.licitacao AS L_LIC
                    ON L_LIC.exercicio_mapa=C_MC.exercicio_mapa
                    AND L_LIC.cod_mapa=C_MC.cod_mapa
                    
                    LEFT JOIN compras.tipo_objeto
                      ON tipo_objeto.cod_tipo_objeto = L_LIC.cod_tipo_objeto
                      
                    LEFT JOIN (
                            SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidade')."')
                                                                       VALUES (cod_licitacao		INTEGER
                                                                              ,cod_modalidade		INTEGER
                                                                              ,cod_entidade		INTEGER
                                                                              ,exercicio			CHAR(4)
                                                                              ,exercicio_licitacao	VARCHAR
                                                                              ,num_licitacao		TEXT ) 
                    ) AS config_licitacao
                    ON config_licitacao.cod_entidade = L_LIC.cod_entidade
                    AND config_licitacao.cod_licitacao = L_LIC.cod_licitacao
                    AND config_licitacao.cod_modalidade = L_LIC.cod_modalidade
                    AND config_licitacao.exercicio = L_LIC.exercicio  

                    LEFT JOIN compras.cotacao_fornecedor_item
                      ON C_JI.exercicio = cotacao_fornecedor_item.exercicio
                     AND C_JI.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                     AND C_JI.cod_item = cotacao_fornecedor_item.cod_item
                     AND C_JI.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                     AND C_JI.lote = cotacao_fornecedor_item.lote
                    
                    LEFT JOIN empenho.atributo_empenho_valor AS E_AEVALOR
                    ON E_AEVALOR.cod_pre_empenho=EE.cod_pre_empenho
                    AND E_AEVALOR.valor= (select valor from empenho.atributo_empenho_valor where cod_pre_empenho=EE.cod_pre_empenho and atributo_empenho_valor.exercicio=EE.exercicio AND atributo_empenho_valor.cod_atributo=101 AND atributo_empenho_valor.cod_cadastro=1 order by timestamp desc limit 1)
                    AND E_AEVALOR.exercicio=EE.exercicio
                    AND E_AEVALOR.cod_atributo=101
                    AND E_AEVALOR.cod_cadastro=1
                    
                    LEFT JOIN tcemg.empenho_registro_precos
                    ON empenho_registro_precos.cod_entidade=EE.cod_entidade
                    AND empenho_registro_precos.cod_empenho=EE.cod_empenho
                    AND empenho_registro_precos.exercicio_empenho=EE.exercicio

                    LEFT JOIN tcemg.registro_precos AS RegPreco
                     ON RegPreco.numero_registro_precos = empenho_registro_precos.numero_registro_precos
                    AND RegPreco.exercicio      = empenho_registro_precos.exercicio
                    AND RegPreco.cod_entidade           = empenho_registro_precos.cod_entidade
                    AND RegPreco.interno                = empenho_registro_precos.interno
                    AND RegPreco.numcgm_gerenciador     = empenho_registro_precos.numcgm_gerenciador

                    LEFT JOIN tcemg.registro_precos_orgao AS RegPrecoOrgao
                     ON RegPreco.numero_registro_precos = RegPrecoOrgao.numero_registro_precos
                    AND RegPreco.exercicio      = RegPrecoOrgao.exercicio_registro_precos
                    AND RegPreco.cod_entidade           = RegPrecoOrgao.cod_entidade
                    AND RegPreco.interno                = RegPrecoOrgao.interno
                    AND RegPreco.numcgm_gerenciador     = RegPrecoOrgao.numcgm_gerenciador
                    
                    LEFT JOIN tcemg.arquivo_emp
                    ON  arquivo_emp.exercicio    = EE.exercicio
                    AND arquivo_emp.cod_empenho  = EE.cod_empenho
                    AND arquivo_emp.cod_entidade = EE.cod_entidade
                
                    WHERE EE.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND EE.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') --ENTRADA MES
                    AND EE.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                    GROUP BY codOrgao,codunidadesub,codfuncao,codsubfuncao,codprograma,idacao,naturezadespesa,subelemento,nroempenho,dtempenho,modalidadeempenho,tpempenho,vlbruto,especificacaoempenho,despdeccontrato,
                    codorgaorespcontrato,codunidadesubrespcontrato,nrocontrato,dataassinaturacontrato,nrosequencialtermoaditivo,despdecconvenio,nroconvenio,dataassinaturaconvenio,despdeclicitacao,nroProcessoLicitatorio,
                    tipoProcesso,cpfOrdenador,C_CD.cod_compra_direta,tipo_objeto.cod_tipo_objeto,L_LIC.exercicio, exercicioProcessoLicitatorio, codUnidadeSubRespLicit, config_licitacao.exercicio_licitacao, config_licitacao.num_licitacao ";
        
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosEMP11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosEMP11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosEMP11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosEMP11()
    {
        $stSql  = " SELECT DISTINCT ON(EE.cod_empenho)
                    11 AS tipoRegistro
                    , LPAD((LPAD(''||OD.num_orgao,2, '0')||LPAD(''||OD.num_unidade,2, '0')), 5, '0') AS codunidadesub
                    , EE.cod_empenho AS nroempenho
                    , recurso.cod_fonte AS codFontRecursos
                    , REPLACE(empenho.fn_consultar_valor_empenhado(
                            EE.exercicio
                            ,EE.cod_empenho
                            ,EE.cod_entidade)::TEXT, '.', ',')
                    AS valorFonte
                    FROM empenho.empenho AS EE
                    
                    LEFT JOIN empenho.empenho_anulado AS EEANUL
                    ON EEANUL.exercicio=EE.exercicio
                    AND EEANUL.cod_entidade=EE.cod_entidade
                    AND EEANUL.cod_empenho=EE.cod_empenho
                    
                    INNER JOIN empenho.pre_empenho AS EPE
                    ON EPE.cod_pre_empenho=EE.cod_pre_empenho
                    AND EPE.exercicio=EE.exercicio
                    INNER JOIN empenho.pre_empenho_despesa AS EPED
                    ON EPED.cod_pre_empenho=EPE.cod_pre_empenho
                    AND EPED.exercicio=EPE.exercicio
                    INNER JOIN orcamento.despesa AS OD
                    ON OD.exercicio=EPED.exercicio AND OD.cod_despesa=EPED.cod_despesa
                    INNER JOIN orcamento.recurso
                    ON recurso.exercicio=OD.exercicio
                    AND recurso.cod_recurso=OD.cod_recurso
                
                    WHERE EE.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND EE.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') --ENTRADA MES
                    AND EE.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                    --ORDER BY EE.cod_entidade,EE.cod_empenho ASC";

        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosEMP12.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosEMP12(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosEMP12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosEMP12()
    {
        $stSql  = "
                SELECT * FROM
                (
                    SELECT DISTINCT ON(EE.cod_empenho)
                    12 AS tipoRegistro
                    , LPAD((LPAD(''||OD.num_orgao,2, '0')||LPAD(''||OD.num_unidade,2, '0')), 5, '0') AS codunidadesub
                    , EE.cod_empenho AS nroempenho
                    
                    ,   CASE WHEN CGM_PJ.cnpj IS NOT NULL THEN
                                2
                             WHEN CGM_PF.cpf IS NOT NULL THEN
                                1
                             ELSE
                                2
                        END
                    AS tipoDocumento
                    
                    ,   CASE WHEN CGM_PJ.cnpj IS NOT NULL THEN
                                CGM_PJ.cnpj
                            WHEN CGM_PF.cpf IS NOT NULL THEN
                                CGM_PF.cpf
                            ELSE
                                (SELECT cnpj FROM sw_cgm_pessoa_juridica  WHERE numcgm = (SELECT numcgm FROM orcamento.entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = ".$this->getDado('entidade')."))
                        END
                    AS nroDocumento
                    
                    ,   CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 9, 2) IN ('01','03','04','05','09','11','16','48','94') THEN
                            true
                        WHEN ''||SUBSTR(conta_despesa.cod_estrutural, 9, 2)||SUBSTR(conta_despesa.cod_estrutural, 12, 2) IN ('3626','3628','3699') THEN
                            true
                        WHEN SUBSTR(conta_despesa.cod_estrutural, 0, 14) IN ('3.1.9.0.92.01','3.1.9.0.92.02','3.1.7.1.92.01','3.1.7.1.92.02',
                        '3.1.9.1.92.01','3.1.9.1.92.02','3.3.9.0.36.07') THEN
                            true
                        ELSE
                            false
                        END
                    AS folha
                    
                    FROM empenho.empenho AS EE
                    
                    LEFT JOIN empenho.empenho_anulado AS EEANUL
                    ON EEANUL.exercicio=EE.exercicio
                    AND EEANUL.cod_entidade=EE.cod_entidade
                    AND EEANUL.cod_empenho=EE.cod_empenho
                    
                    INNER JOIN empenho.pre_empenho AS EPE
                    ON EPE.cod_pre_empenho=EE.cod_pre_empenho
                    AND EPE.exercicio=EE.exercicio
                    INNER JOIN empenho.pre_empenho_despesa AS EPED
                    ON EPED.cod_pre_empenho=EPE.cod_pre_empenho
                    AND EPED.exercicio=EPE.exercicio
                    INNER JOIN orcamento.despesa AS OD
                    ON OD.exercicio=EPED.exercicio AND OD.cod_despesa=EPED.cod_despesa
                    INNER JOIN orcamento.conta_despesa
                    ON conta_despesa.exercicio=OD.exercicio
                    AND conta_despesa.cod_conta=OD.cod_conta
                    
                    LEFT JOIN sw_cgm_pessoa_juridica AS CGM_PJ
                    ON CGM_PJ.numcgm=EPE.cgm_beneficiario
                    LEFT JOIN sw_cgm_pessoa_fisica AS CGM_PF
                    ON CGM_PF.numcgm=EPE.cgm_beneficiario
                    LEFT JOIN sw_cgm AS CGM
                    ON CGM.numcgm=EPE.cgm_beneficiario
                
                    WHERE EE.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                    AND EE.dt_empenho BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') --ENTRADA MES
                    AND EE.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
                ) AS tabela
                ";

        return $stSql;
    }
    
    public function __destruct(){}

}
