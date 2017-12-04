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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_DOCUMENTO
    * Data de Criação: 22/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaDocumento.class.php 66396 2016-08-24 14:21:29Z evandro $

* Casos de uso: uc-05.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATDividaDocumento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaDocumento()
    {
        parent::Persistente();
        $this->setTabela('divida.documento');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento');
        $this->AddCampo('num_parcelamento','integer',true,'',true,true);
        $this->AddCampo('cod_tipo_documento','integer',true,'',true,true);
        $this->AddCampo('cod_documento','integer',true,'',true,true);
    }

    public function recuperaListaDocumento(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumento()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     ddd.*, \n";
        $stSql .= "     amd.nome_documento, \n";
        $stSql .= "     ded.num_documento \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.documento AS ddd \n";
        $stSql .= " INNER JOIN divida.emissao_documento AS ded
                       USING(num_parcelamento, cod_tipo_documento, cod_documento) \n";

        $stSql .= " INNER JOIN \n";
        $stSql .= "     administracao.modelo_documento AS amd \n";
        $stSql .= " ON \n";
        $stSql .= "     amd.cod_documento = ddd.cod_documento \n";
        $stSql .= "     AND amd.cod_tipo_documento = ddd.cod_tipo_documento \n";

        return $stSql;
    }

    public function recuperaListaDocumentoPopUp(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentoPopUp().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentoPopUp()
    {
        $stSql  = " SELECT \n";
        $stSql .= "     ddd.*, \n";
        $stSql .= "     ddp.cod_inscricao, \n";
        $stSql .= "     ddp.exercicio, \n";
        $stSql .= "     ( \n";
        $stSql .= "         SELECT \n";
        $stSql .= "             swc.nom_cgm \n";
        $stSql .= "         FROM \n";
        $stSql .= "             sw_cgm AS swc \n";
        $stSql .= "         WHERE \n";
        $stSql .= "             swc.numcgm = ddc.numcgm \n";
        $stSql .= "     )AS nom_cgm, \n";
        $stSql .= "     ddc.numcgm, \n";
        $stSql .= "     amd.nome_documento, \n";
        $stSql .= "     amd.nome_arquivo_agt, \n";
        $stSql .= "     ded.num_documento, \n";
        $stSql .= "     ded.cod_tipo_documento \n";
        $stSql .= " FROM \n";
        $stSql .= "     divida.documento AS ddd \n";
        $stSql .= " INNER JOIN divida.emissao_documento AS ded
                       USING(num_parcelamento, cod_tipo_documento, cod_documento) \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     divida.divida_parcelamento AS ddp \n";
        $stSql .= " ON \n";
        $stSql .= "     ddp.num_parcelamento = ddd.num_parcelamento \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     divida.divida_cgm AS ddc \n";
        $stSql .= " ON \n";
        $stSql .= "     ddc.exercicio = ddp.exercicio \n";
        $stSql .= "     AND ddc.cod_inscricao = ddp.cod_inscricao \n";
        $stSql .= " INNER JOIN \n";
        $stSql .= "     administracao.modelo_documento AS amd \n";
        $stSql .= " ON \n";
        $stSql .= "     amd.cod_documento = ddd.cod_documento \n";
        $stSql .= "     AND amd.cod_tipo_documento = ddd.cod_tipo_documento \n";

        return $stSql;
    }

    //utilizado no LSMAnterEmissao.php
    public function recuperaListaDocumentoLS(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentoLS().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentoLS()
    {
        /* 
            Caso a consulta NAO seja executada pela acao Gestão Tributária :: Dívida Ativa :: Cobrança Administrativa :: Cobrar Dívida Ativa
            Concatena a string 'divida_ativa.cod_inscricao AS cod_inscricao_divida_ativa,' 
        */
        $stSql  = " SELECT DISTINCT 
                        ddd.*, 
                        ".$this->getDado('cod_inscricao_divida_ativa')."
                        divida_ativa.exercicio AS exercicio_divida_ativa,  
                        ded.num_emissao, 
                        ded.num_documento, 
                        ded.exercicio, 
                        CASE WHEN dp.numero_parcelamento = -1 THEN
                                       ' '
                                   ELSE
                                       dp.numero_parcelamento::text
                                   END AS numero_parcelamento, 
                        CASE WHEN dp.exercicio = '-1' THEN
                                       ' '
                                   ELSE
                                       dp.exercicio::text
                                   END AS exercicio_cobranca,  
                        ( 
                            SELECT 
                                swc.nom_cgm 
                            FROM 
                                sw_cgm AS swc 
                            WHERE 
                                swc.numcgm = ddc.numcgm 
                        )AS nom_cgm, 
                        ddc.numcgm, 
                        amd.nome_documento, 
                        amd.nome_arquivo_agt, 
                        aad.nome_arquivo_swx, 
                        (   SELECT to_char(emissao_documento.timestamp, 'dd/mm/YYYY')
                              FROM divida.emissao_documento
                            WHERE emissao_documento.num_parcelamento = ded.num_parcelamento
                              AND emissao_documento.cod_tipo_documento = ded.cod_tipo_documento
                              AND emissao_documento.cod_documento = ded.cod_documento
                              AND emissao_documento.num_documento = ded.num_documento
                            ORDER BY emissao_documento.timestamp ASC
                            LIMIT 1
                        ) AS data_emissao, 
                        (   SELECT emissao_documento.num_emissao
                              FROM divida.emissao_documento
                            WHERE emissao_documento.num_parcelamento = ded.num_parcelamento
                              AND emissao_documento.cod_tipo_documento = ded.cod_tipo_documento
                              AND emissao_documento.cod_documento = ded.cod_documento
                              AND emissao_documento.num_documento = ded.num_documento
                            ORDER BY emissao_documento.timestamp DESC
                            LIMIT 1
                        ) AS num_emissao 
                    FROM  divida.documento AS ddd 
                    
                    LEFT JOIN divida.emissao_documento AS ded
                           ON ded.num_parcelamento = ddd.num_parcelamento
                          AND ded.cod_documento = ddd.cod_documento
                          AND ded.cod_tipo_documento = ddd.cod_tipo_documento 
                    
                    INNER JOIN divida.divida_parcelamento AS ddp 
                            ON ddp.num_parcelamento = ddd.num_parcelamento 

                    INNER JOIN divida.parcelamento AS dp 
                            ON dp.num_parcelamento = ddd.num_parcelamento 

                    INNER JOIN divida.divida_cgm AS ddc 
                            ON ddc.exercicio = ddp.exercicio 
                           AND ddc.cod_inscricao = ddp.cod_inscricao 
         
                    INNER JOIN administracao.modelo_documento AS amd 
                            ON amd.cod_documento = ddd.cod_documento 
                           AND amd.cod_tipo_documento = ddd.cod_tipo_documento 

                    INNER JOIN administracao.modelo_arquivos_documento AS amad 
                            ON amad.cod_documento = ddd.cod_documento 
                           AND amad.cod_tipo_documento = ddd.cod_tipo_documento 

                    INNER JOIN administracao.arquivos_documento AS aad 
                            ON aad.cod_arquivo = amad.cod_arquivo 

                    LEFT JOIN divida.divida_imovel AS ddi 
                           ON ddi.exercicio = ddp.exercicio 
                          AND ddi.cod_inscricao = ddp.cod_inscricao 
                    
                    LEFT JOIN divida.divida_empresa AS dde 
                           ON dde.exercicio = ddp.exercicio 
                          AND dde.cod_inscricao = ddp.cod_inscricao

                    LEFT JOIN divida.divida_cancelada AS ddcanc
                           ON ddcanc.cod_inscricao = ddp.cod_inscricao
                          AND ddcanc.exercicio = ddp.exercicio

                    LEFT JOIN divida.divida_remissao
                           ON divida_remissao.cod_inscricao = ddp.cod_inscricao
                          AND divida_remissao.exercicio = ddp.exercicio
                        
                    LEFT JOIN divida.divida_ativa
                           ON divida_ativa.cod_inscricao = ddp.cod_inscricao
                          AND divida_ativa.exercicio = ddp.exercicio  
                        
                    LEFT JOIN divida.modalidade_vigencia
                           ON modalidade_vigencia.cod_modalidade = dp.cod_modalidade 
                          AND modalidade_vigencia.timestamp = dp.timestamp_modalidade
                        
                    LEFT JOIN divida.modalidade
                           ON modalidade.cod_modalidade = modalidade_vigencia.cod_modalidade   
            ";
        return $stSql;
    }

    public function recuperaListaNumParcelamentoPorDocumento(&$rsRecordSet, $inNumDocumento, $inCodDocumento, $inCodTipoDocumento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaNumParcelamentoPorDocumento($inNumDocumento, $inCodDocumento, $inCodTipoDocumento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaNumParcelamentoPorDocumento($inNumDocumento, $inCodDocumento, $inCodTipoDocumento)
    {
        $stSql  = " SELECT lista_numparcelamento_por_documento(".$inNumDocumento.", ".$inCodDocumento.", ".$inCodTipoDocumento.") AS numparcelamento ";

        return $stSql;
    }

    public function recuperaListaDocumentoReemissaoLScombo(&$rsRecordSet, $stCondicao, $stCondicao2, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentoReemissaoLScombo($stCondicao, $stCondicao2);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentoReemissaoLScombo($stCondicao='',$stCondicao2='')
    {
        $stSql = "
                SELECT MAX(EMISSAO_DOCUMENTO.NUM_EMISSAO) AS  NUM_EMISSAO
                         , EMISSAO_DOCUMENTO.cod_tipo_documento
                         , EMISSAO_DOCUMENTO.cod_documento
                         , EMISSAO_DOCUMENTO.num_documento
                         , EMISSAO_DOCUMENTO.exercicio
                         , TO_CHAR(MAX(EMISSAO_DOCUMENTO.timestamp),'dd/mm/YYYY') AS data_emissao
                         , DIVIDA_DOCUMENTO.nom_cgm
                         , DIVIDA_DOCUMENTO.numcgm
                         , DIVIDA_DOCUMENTO.nome_documento
                         , DIVIDA_DOCUMENTO.nome_arquivo_agt
                         , DIVIDA_DOCUMENTO.nome_arquivo_swx
                         , DIVIDA_DOCUMENTO.REMIDO
                         , lista_cobranca_por_documento ( EMISSAO_DOCUMENTO.num_documento, EMISSAO_DOCUMENTO.cod_documento, EMISSAO_DOCUMENTO.cod_tipo_documento ) AS cobranca
                         , lista_inscricao_por_documento( EMISSAO_DOCUMENTO.num_documento, EMISSAO_DOCUMENTO.cod_documento, EMISSAO_DOCUMENTO.cod_tipo_documento, EMISSAO_DOCUMENTO.exercicio ) AS inscricoes
                      FROM DIVIDA.EMISSAO_DOCUMENTO
                INNER JOIN
                         (   SELECT DOCUMENTO.cod_tipo_documento
                                  , DOCUMENTO.cod_documento
                                  , DOCUMENTO.num_parcelamento
                                  , SW_CGM.nom_cgm
                                  , SW_CGM.numcgm
                                  , MODELO_DOCUMENTO.nome_documento
                                  , MODELO_DOCUMENTO.nome_arquivo_agt
                                  , ARQUIVOS_DOCUMENTO.nome_arquivo_swx
                                  , CASE WHEN ( divida_remissao.cod_inscricao IS NOT NULL ) THEN
                                      TRUE
                                    ELSE
                                        FALSE
                                    END  AS REMIDO
                               FROM DIVIDA.DOCUMENTO
                         INNER JOIN DIVIDA.DIVIDA_PARCELAMENTO
                                 ON DOCUMENTO.NUM_PARCELAMENTO = DIVIDA_PARCELAMENTO.NUM_PARCELAMENTO
                         INNER JOIN ADMINISTRACAO.MODELO_DOCUMENTO
                                 ON DOCUMENTO.COD_TIPO_DOCUMENTO = MODELO_DOCUMENTO.COD_TIPO_DOCUMENTO
                                AND DOCUMENTO.COD_DOCUMENTO  = MODELO_DOCUMENTO.COD_DOCUMENTO
                         INNER JOIN
                                  ( SELECT DISTINCT
                                           COD_DOCUMENTO
                                         , COD_TIPO_DOCUMENTO
                                         , COD_ARQUIVO
                                     FROM  ADMINISTRACAO.MODELO_ARQUIVOS_DOCUMENTO
                                  ) AS MODELO_ARQUIVOS_DOCUMENTO
                                 ON DOCUMENTO.COD_DOCUMENTO = MODELO_ARQUIVOS_DOCUMENTO.COD_DOCUMENTO
                                AND DOCUMENTO.COD_TIPO_DOCUMENTO = MODELO_ARQUIVOS_DOCUMENTO.COD_TIPO_DOCUMENTO
                         INNER JOIN ADMINISTRACAO.ARQUIVOS_DOCUMENTO
                                 ON MODELO_ARQUIVOS_DOCUMENTO.COD_ARQUIVO = ARQUIVOS_DOCUMENTO.COD_ARQUIVO
                         INNER JOIN DIVIDA.DIVIDA_CGM
                                 ON DIVIDA_PARCELAMENTO.COD_INSCRICAO = DIVIDA_CGM.COD_INSCRICAO
                                AND DIVIDA_PARCELAMENTO.EXERCICIO = DIVIDA_CGM.EXERCICIO
                         INNER JOIN SW_CGM
                                 ON DIVIDA_CGM.NUMCGM = SW_CGM.NUMCGM";

        if ( strpos($stCondicao, 'DIVIDA_IMOVEL') ) {
            $stSql .= " INNER JOIN DIVIDA.DIVIDA_IMOVEL
                                ON DIVIDA_PARCELAMENTO.COD_INSCRICAO = DIVIDA_IMOVEL.COD_INSCRICAO
                               AND DIVIDA_PARCELAMENTO.EXERCICIO = DIVIDA_IMOVEL.EXERCICIO";
        }

        if ( strpos($stCondicao, 'DIVIDA_EMPRESA') ) {
            $stSql .= " INNER JOIN DIVIDA.DIVIDA_EMPRESA
                                ON DIVIDA_PARCELAMENTO.COD_INSCRICAO = DIVIDA_EMPRESA.COD_INSCRICAO
                               AND DIVIDA_PARCELAMENTO.EXERCICIO = DIVIDA_EMPRESA.EXERCICIO";
        }

        $stSql .= "           LEFT JOIN DIVIDA.DIVIDA_REMISSAO
                                     ON DIVIDA_PARCELAMENTO.COD_INSCRICAO = DIVIDA_REMISSAO.COD_INSCRICAO
                                    AND DIVIDA_PARCELAMENTO.EXERCICIO = DIVIDA_REMISSAO.EXERCICIO
                                  WHERE (1=1)
                                        ".$stCondicao."
                              ) AS DIVIDA_DOCUMENTO
                             ON DIVIDA_DOCUMENTO.cod_tipo_documento = EMISSAO_DOCUMENTO.cod_tipo_documento
                            AND DIVIDA_DOCUMENTO.cod_documento      = EMISSAO_DOCUMENTO.cod_documento
                            AND DIVIDA_DOCUMENTO.num_parcelamento   = EMISSAO_DOCUMENTO.num_parcelamento
                          WHERE (1=1)
                                ".$stCondicao2."

                            --    AND ddcanc.cod_inscricao is null
                            --    AND ddcanc.exercicio is null

                       GROUP BY EMISSAO_DOCUMENTO.cod_tipo_documento
                              , EMISSAO_DOCUMENTO.cod_documento
                              , EMISSAO_DOCUMENTO.num_documento
                              , EMISSAO_DOCUMENTO.exercicio
                              , DIVIDA_DOCUMENTO.nom_cgm
                              , DIVIDA_DOCUMENTO.numcgm
                              , DIVIDA_DOCUMENTO.nome_documento
                              , DIVIDA_DOCUMENTO.nome_arquivo_agt
                              , DIVIDA_DOCUMENTO.nome_arquivo_swx
                              , DIVIDA_DOCUMENTO.REMIDO";

        return $stSql;
    }

    public function criaTabelasDocumentos($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaCriaTabelasDocumentos();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaCriaTabelasDocumentos()
    {
        $stSql = "
                CREATE TEMPORARY TABLE tmp_ded AS
                    SELECT tmp.*
                          FROM divida.emissao_documento AS tmp
                    INNER JOIN ( SELECT max(timestamp) as timestamp
                                      , cod_tipo_documento
                                      , cod_documento
                                      , num_parcelamento
                                   FROM divida.emissao_documento
                               GROUP BY cod_tipo_documento
                                      , cod_documento
                                      , num_parcelamento
                               )AS tmp2
                            ON tmp2.cod_tipo_documento = tmp.cod_tipo_documento
                           AND tmp2.cod_documento = tmp.cod_documento
                           AND tmp2.num_parcelamento = tmp.num_parcelamento
                           AND tmp2.timestamp = tmp.timestamp;

                CREATE TEMPORARY TABLE tmp_dedm AS
                    SELECT tmp.*
                          FROM divida.emissao_documento AS tmp
                    INNER JOIN ( SELECT min(timestamp) as timestamp
                                      , cod_tipo_documento
                                      , cod_documento
                                      , num_parcelamento
                                   FROM divida.emissao_documento
                               GROUP BY cod_tipo_documento
                                      , cod_documento
                                      , num_parcelamento
                               )AS tmp2
                            ON tmp2.cod_tipo_documento = tmp.cod_tipo_documento
                           AND tmp2.cod_documento = tmp.cod_documento
                           AND tmp2.num_parcelamento = tmp.num_parcelamento
                           AND tmp2.timestamp = tmp.timestamp;
        
                CREATE TEMPORARY TABLE tmp_ddp AS
                    SELECT MAX(dp.num_parcelamento) AS num_parcelamento
                             , dp.cod_inscricao
                             , dp.exercicio
                          FROM divida.divida_parcelamento AS dp
                     LEFT JOIN divida.parcelamento_cancelamento
                            ON parcelamento_cancelamento.num_parcelamento = dp.num_parcelamento
                         WHERE parcelamento_cancelamento.num_parcelamento IS NULL
                         GROUP BY dp.cod_inscricao , dp.exercicio;
        ";

        return $stSql;
    }

    public function deletaTabelaDocumentos($boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaDeletaTabelaDocumentos();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaDeletaTabelaDocumentos()
    {
        $stSql = "
                DROP TABLE tmp_ded;
                DROP TABLE tmp_dedm;
                DROP TABLE tmp_ddp;
            ";

        return $stSql;
    }

    public function recuperaListaDocumentoLScombo(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaDocumentoLScombo($stCondicao).$stOrdem;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaDocumentoLScombo($stCondicao='')
    {
        $stSql  = "
            SELECT DISTINCT ddd.*
                    , tmp_ded.num_emissao
                    , tmp_ded.num_documento
                    , tmp_ded.exercicio
                    , CASE WHEN dp.numero_parcelamento = -1 THEN
                            ' '
                      ELSE
                            dp.numero_parcelamento::text
                      END AS numero_parcelamento
                    , CASE WHEN dp.exercicio = '-1' THEN
                            ' '
                      ELSE
                            dp.exercicio::text
                      END AS exercicio_cobranca
                    , swc.nom_cgm
                    , ddc.numcgm
                    , amd.nome_documento
                    , amd.nome_arquivo_agt
                    , aad.nome_arquivo_swx
                    , to_char(tmp_dedm.timestamp, 'dd/mm/YYYY') AS data_emissao
                    , dp.cod_modalidade
                    , ddp.cod_inscricao || '/' || ddp.exercicio || '<br>' AS inscricoes
                    --, lista_inscricao_por_num_parcelamento(dp.num_parcelamento) AS inscricoes
            FROM divida.documento as ddd
            INNER JOIN tmp_ddp AS ddp
                   ON ddp.num_parcelamento = ddd.num_parcelamento
            LEFT JOIN tmp_ded
                   ON tmp_ded.cod_tipo_documento = ddd.cod_tipo_documento
                  AND tmp_ded.cod_documento      = ddd.cod_documento
                  AND tmp_ded.num_parcelamento   = ddd.num_parcelamento

            LEFT JOIN tmp_dedm
                   ON tmp_dedm.cod_tipo_documento = ddd.cod_tipo_documento
                  AND tmp_dedm.cod_documento      = ddd.cod_documento
                  AND tmp_dedm.num_parcelamento   = ddd.num_parcelamento
    ";
        if ( strpos($stCondicao, 'ddi') ) {
        $stSql .= " LEFT JOIN divida.divida_imovel AS ddi
                      ON ddi.exercicio = ddp.exercicio
                     AND ddi.cod_inscricao = ddp.cod_inscricao \n";
        }

        if ( strpos($stCondicao, 'dde') ) {
        $stSql .= " LEFT JOIN divida.divida_empresa AS dde
                      ON dde.exercicio = ddp.exercicio
                     AND dde.cod_inscricao = ddp.cod_inscricao \n";
        }

        $stSql .= "

            INNER JOIN divida.parcelamento AS dp
                   ON dp.num_parcelamento = ddd.num_parcelamento

            INNER JOIN divida.divida_cgm AS ddc
                   ON ddc.exercicio = ddp.exercicio
                  AND ddc.cod_inscricao = ddp.cod_inscricao

            INNER JOIN sw_cgm AS swc
                   ON swc.numcgm = ddc.numcgm
            
            INNER JOIN divida.modalidade_documento
                ON modalidade_documento.cod_tipo_documento      = ddd.cod_tipo_documento
                AND modalidade_documento.cod_documento          = ddd.cod_documento
            
            INNER JOIN administracao.modelo_documento AS amd
                   ON amd.cod_documento = ddd.cod_documento
                  AND amd.cod_tipo_documento = ddd.cod_tipo_documento
            INNER JOIN administracao.modelo_arquivos_documento AS amad
                   ON amad.cod_documento = ddd.cod_documento
                  AND amad.cod_tipo_documento = ddd.cod_tipo_documento
            INNER JOIN administracao.arquivos_documento AS aad
                   ON aad.cod_arquivo = amad.cod_arquivo
            LEFT JOIN divida.divida_cancelada AS ddcanc
                   ON ddcanc.cod_inscricao = ddp.cod_inscricao
                  AND ddcanc.exercicio = ddp.exercicio
            LEFT JOIN divida.divida_remissao
                   ON divida_remissao.cod_inscricao = ddp.cod_inscricao
                  AND divida_remissao.exercicio = ddp.exercicio
                WHERE CASE WHEN ( divida_remissao.cod_inscricao IS NOT NULL ) THEN
                           CASE WHEN ( ddd.cod_tipo_documento = 7 ) THEN
                                true
                           ELSE
                                false
                           END
                      ELSE
                           true
                      END

                AND ddcanc.cod_inscricao IS NULL
                AND ddcanc.exercicio IS NULL
            ".$stCondicao."
    ";

        return $stSql;
    }

    public function recuperaNumeroDocumento(&$rsRecordSet, $inCodTipoDocumento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaNumeroDocumento($inCodTipoDocumento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaNumeroDocumento($inCodTipoDocumento)
    {
        ;

        $stSql  = " SELECT													\n";
        $stSql .= "		coalesce ( (max(ddc.num_documento)+1), 1 ) as valor	\n";
        $stSql .= "	FROM													\n";
        $stSql .= "		divida.documento as ddc			        			\n";
        $stSql .= " WHERE                                                   \n";
        $stSql .= "     ddc.exercicio = ".Sessao::getExercicio()."              \n";
        $stSql .= "     AND ddc.cod_tipo_documento = ".$inCodTipoDocumento." \n";

        return $stSql;
    }

    public function recuperaListaCarnesCobrancaEstornar(&$rsRecordSet, $inNumParcelamento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaCarnesCobrancaEstornar($inNumParcelamento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaListaCarnesCobrancaEstornar($inNumParcelamento)
    {
        $stSql = "
            SELECT DISTINCT
                carne.numeracao,
                carne.cod_convenio

            FROM
                divida.parcela AS dp

            INNER JOIN
                divida.parcela_calculo
            ON
                dp.num_parcelamento = parcela_calculo.num_parcelamento
                AND dp.num_parcela = parcela_calculo.num_parcela

            INNER JOIN
                arrecadacao.lancamento_calculo
            ON
                lancamento_calculo.cod_calculo = parcela_calculo.cod_calculo

            INNER JOIN
                arrecadacao.parcela AS ap
            ON
                ap.cod_lancamento = lancamento_calculo.cod_lancamento

            INNER JOIN
                arrecadacao.carne
            ON
                carne.cod_parcela = ap.cod_parcela

            LEFT JOIN
                arrecadacao.pagamento
            ON
                pagamento.numeracao = carne.numeracao

            LEFT JOIN
                arrecadacao.carne_devolucao
            ON
                carne_devolucao.numeracao = carne.numeracao

            WHERE
                carne_devolucao.numeracao IS NULL
                AND pagamento.numeracao IS NULL
                AND dp.cancelada = false
                AND dp.paga = false
                AND dp.num_parcelamento = ".$inNumParcelamento;

        return $stSql;
    }

    public function recuperaListaCobrancaEstornar(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaCobrancaEstornar().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaCobrancaEstornar()
    {
        $stSql  = "
            SELECT DISTINCT
                dp.num_parcelamento,
                dparc.numero_parcelamento,
                dparc.exercicio AS exercicio_cobranca,
                ddc.numcgm,
                (
                    SELECT
                        nom_cgm
                    FROM
                        sw_cgm
                    WHERE
                        sw_cgm.numcgm = ddc.numcgm
                )AS nom_cgm,
                ded.num_documento,
                vlr.valor AS valor_parcelamento,
                tot.parcela AS qtd_parcelas,
                CASE WHEN tot_vencida.parcela IS NULL THEN
                    0
                ELSE
                    tot_vencida.parcela
                END AS qtd_parcelas_vencidas,
                lista_inscricao_por_num_parcelamento( dp.num_parcelamento ) AS inscricao,
                CASE WHEN ( max_vencida.dt_vencimento_parcela IS NOT NULL ) THEN
                    to_char(now() - max_vencida.dt_vencimento_parcela, 'dd')::integer
                ELSE
                    0
                END AS dias_atraso
            FROM
                divida.documento AS dd

            LEFT JOIN
                divida.emissao_documento AS ded
            ON
                ded.num_parcelamento = dd.num_parcelamento
                AND ded.cod_documento = dd.cod_documento
                AND ded.cod_tipo_documento = dd.cod_tipo_documento

            INNER JOIN
                divida.divida_parcelamento AS ddp
            ON
                ddp.num_parcelamento = dd.num_parcelamento

            INNER JOIN
                divida.parcelamento AS dparc
            ON
                dparc.num_parcelamento = dd.num_parcelamento

            INNER JOIN
                divida.divida_cgm AS ddc
            ON
                ddc.cod_inscricao = ddp.cod_inscricao
                AND ddc.exercicio = ddp.exercicio

            LEFT JOIN
                divida.divida_cancelada AS ddcanc
            ON
                ddcanc.cod_inscricao = ddp.cod_inscricao
                AND ddcanc.exercicio = ddp.exercicio

            INNER JOIN
                (
                    SELECT
                        SUM(vlr_parcela) AS valor,
                        num_parcelamento
                    FROM
                        divida.parcela
                    WHERE
                        paga = false
                        AND cancelada = false
                    GROUP BY
                        num_parcelamento
                ) AS vlr
            ON
                vlr.num_parcelamento = dd.num_parcelamento

            INNER JOIN
                (
                    SELECT
                        COUNT(num_parcela) AS parcela,
                        num_parcelamento
                    FROM
                        divida.parcela
                    WHERE
                        paga = false
                        AND cancelada = false
                    GROUP BY
                        num_parcelamento
                ) AS tot
            ON
                tot.num_parcelamento = dd.num_parcelamento

            LEFT JOIN
                (
                    SELECT
                        COUNT(num_parcela) AS parcela,
                        num_parcelamento
                    FROM
                        divida.parcela
                    WHERE
                        paga = false
                        AND cancelada = false
                        AND dt_vencimento_parcela < now()
                    GROUP BY
                        num_parcelamento
                ) AS tot_vencida
            ON
                tot_vencida.num_parcelamento = dd.num_parcelamento

            LEFT JOIN
                (
                    SELECT
                        min(dt_vencimento_parcela) AS dt_vencimento_parcela,
                        num_parcelamento
                    FROM
                        divida.parcela
                    WHERE
                        paga = false
                        AND cancelada = false
                        AND now() > dt_vencimento_parcela
                    GROUP BY
                        num_parcelamento
                )AS max_vencida
            ON
                max_vencida.num_parcelamento = dd.num_parcelamento

            INNER JOIN
                divida.parcela AS dp
            ON
                dp.num_parcelamento = dd.num_parcelamento
        \n";

        return $stSql;
    }

    public function recuperaTipoDocumentoUltimoParcelamento(&$rsRecordSet, $stFiltro, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaTipoDocumentoUltimoParcelamento().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTipoDocumentoUltimoParcelamento()
    {
        $stSql  = "       SELECT
            ddp.cod_inscricao
            , ddp.exercicio
            , ddp.num_parcelamento
            , ddoc.cod_tipo_documento
        FROM
            divida.divida_parcelamento as ddp

            INNER JOIN divida.parcelamento AS dp
            ON dp.num_parcelamento = ddp.num_parcelamento

            LEFT JOIN (
                ( SELECT
                    busca1.num_parcelamento
                    , busca2.cod_tipo_documento
                FROM
                    (
                        select
                            max(ded.num_documento) as num_documento
                            , ddoc.num_parcelamento
                        from
                            divida.documento as ddoc
                            INNER JOIN divida.emissao_documento as ded
                            ON ded.num_parcelamento = ddoc.num_parcelamento
                            and ded.cod_documento = ddoc.cod_documento
                            and ded.cod_tipo_documento = ddoc.cod_tipo_documento
                        GROUP BY
                            ddoc.num_parcelamento
                    ) as busca1
                    INNER JOIN (
                        SELECT
                            num_parcelamento
                            , cod_tipo_documento
                        FROM
                            divida.documento as ddoc
                    ) as busca2
                    ON busca2.num_parcelamento = busca1.num_parcelamento
                )
            ) as ddoc
            ON ddoc.num_parcelamento = ddp.num_parcelamento
         \n";

        return $stSql;
    }

    public function listaDocumentoConsultaDASemCobranca(&$rsRecordSet, $inCodInscricao, $inExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaDocumentoConsultaDASemCobranca( $inCodInscricao, $inExercicio );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaDocumentoConsultaDASemCobranca($inCodInscricao, $inExercicio)
    {
        $stSql  = " SELECT DISTINCT
                        dd.cod_documento,
                        dd.cod_tipo_documento,
                        ded.num_documento,
                        ded.exercicio,
                        to_char(ded.timestamp, 'dd/mm/yyyy') AS dt_emissao,
                        amd.nome_documento,
                        amd.nome_arquivo_agt,
                        dp.num_parcelamento,
                        aad.nome_arquivo_swx,
                        CASE WHEN ded.timestamp IS NULL THEN
                            false
                        ELSE
                            true
                        END AS boImprimir

                    FROM
                        divida.parcelamento AS dp

                    INNER JOIN
                        divida.documento AS dd
                    ON
                        dd.num_parcelamento = dp.num_parcelamento

                    INNER JOIN
                        administracao.modelo_documento AS amd
                    ON
                        dd.cod_documento = amd.cod_documento
                        AND dd.cod_tipo_documento = amd.cod_tipo_documento

                    INNER JOIN
                        administracao.modelo_arquivos_documento AS amad
                    ON
                        dd.cod_documento = amad.cod_documento
                        AND dd.cod_tipo_documento = amad.cod_tipo_documento

                    INNER JOIN
                        administracao.arquivos_documento AS aad
                    ON
                        aad.cod_arquivo = amad.cod_arquivo

                    LEFT JOIN
                    (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_documento,
                                cod_tipo_documento,
                                num_parcelamento,
                                num_documento,
                                exercicio
                            FROM
                                divida.emissao_documento
                            GROUP BY
                                cod_documento,
                                cod_tipo_documento,
                                num_parcelamento,
                                num_documento,
                                exercicio
                        )AS ded
                    ON
                        ded.cod_documento = dd.cod_documento
                        AND ded.cod_tipo_documento = dd.cod_tipo_documento
                        AND ded.num_parcelamento = dd.num_parcelamento

                    WHERE
                        dp.num_parcelamento = (
                            SELECT
                                MIN(num_parcelamento)
                            FROM
                                divida.divida_parcelamento
                            WHERE
                                divida_parcelamento.cod_inscricao = ".$inCodInscricao."
                                AND divida_parcelamento.exercicio = '".$inExercicio."'
                        ) \n";

        return $stSql;
    }

    public function listaDocumentoConsultaDAComCobranca(&$rsRecordSet, $inCodInscricao, $inExercicio, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaDocumentoConsultaDAComCobranca( $inCodInscricao, $inExercicio );
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaDocumentoConsultaDAComCobranca($inCodInscricao, $inExercicio)
    {
        $stSql  = " SELECT DISTINCT
                        dd.cod_documento,
                        dd.cod_tipo_documento,
                        ded.num_documento,
                        ded.exercicio,
                        to_char(ded.timestamp, 'dd/mm/yyyy') AS dt_emissao,
                        amd.nome_documento,
                        amd.nome_arquivo_agt,
                        dp.num_parcelamento,
                        aad.nome_arquivo_swx,
                        CASE WHEN ded.timestamp IS NULL THEN
                            false
                        ELSE
                            true
                        END AS boImprimir

                    FROM
                        divida.divida_parcelamento AS ddp

                    INNER JOIN
                        divida.parcelamento AS dp
                    ON
                        dp.num_parcelamento = ddp.num_parcelamento

                    INNER JOIN
                        divida.documento AS dd
                    ON
                        dd.num_parcelamento = dp.num_parcelamento

                    INNER JOIN
                        administracao.modelo_documento AS amd
                    ON
                        dd.cod_documento = amd.cod_documento
                        AND dd.cod_tipo_documento = amd.cod_tipo_documento

                    INNER JOIN
                        administracao.modelo_arquivos_documento AS amad
                    ON
                        dd.cod_documento = amad.cod_documento
                        AND dd.cod_tipo_documento = amad.cod_tipo_documento

                    INNER JOIN
                        administracao.arquivos_documento AS aad
                    ON
                        aad.cod_arquivo = amad.cod_arquivo

                    LEFT JOIN
                        (
                            SELECT
                                max(timestamp) AS timestamp,
                                cod_documento,
                                cod_tipo_documento,
                                num_parcelamento,
                                num_documento,
                                exercicio
                            FROM
                                divida.emissao_documento
                            GROUP BY
                                cod_documento,
                                cod_tipo_documento,
                                num_parcelamento,
                                num_documento,
                                exercicio
                        )AS ded
                    ON
                        ded.cod_documento = dd.cod_documento
                        AND ded.cod_tipo_documento = dd.cod_tipo_documento
                        AND ded.num_parcelamento = dd.num_parcelamento

                    WHERE
                        dp.numero_parcelamento::integer != -1
                        AND dp.exercicio::integer != -1
                        AND ddp.cod_inscricao = ".$inCodInscricao."
                        AND ddp.exercicio = '".$inExercicio."' \n";

        return $stSql;
    }

}// end of class

?>
