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
 * Classe de mapeamento da tabela tcemg.registros_arquivo_programa
 * Data de Criação: 11/03/2014
 * 
 * @author Analista      : Eduardo Schitz
 * @author Desenvolvedor : Franver Sarmento de Moraes
 * 
 * @package URBEM
 * @subpackage Mapeamento
 * 
 * Casos de uso: uc-02.09.04
 *
 * $Id: TTCEMGRegistroPrecos.class.php 62842 2015-06-26 17:29:59Z michel $
 * $Revision: 62842 $
 * $Author: michel $
 * $Date: 2015-06-26 14:29:59 -0300 (Sex, 26 Jun 2015) $
 * 
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGRegistroPrecos extends Persistente
{
    /**
     * Método construtor
     * @access private
     */
    public function TTCEMGRegistroPrecos()
    {
        parent::Persistente();

        $this->setTabela('tcemg.registro_precos');
        $this->setComplementoChave('cod_entidade, numero_registro_precos, exercicio, interno, numcgm_gerenciador');

        $this->addCampo('cod_entidade'                     , 'integer' , true  , '' ,  true, false );
        $this->addCampo('numero_registro_precos'           , 'integer' , true  , '' ,  true, false );
        $this->addCampo('exercicio'                        , 'varchar' , true  , '4',  true, false );
        $this->addCampo('data_abertura_registro_precos'    , 'date'    , true  , '' , false, false );
        $this->addCampo('numcgm_gerenciador'               , 'integer' , true  , '' ,  true,  true );
        $this->addCampo('exercicio_licitacao'              , 'varchar' , true  , '4', false, false );
        $this->addCampo('numero_processo_licitacao'        , 'varchar' , true  ,'12', false, false );
        $this->addCampo('codigo_modalidade_licitacao'      , 'integer' , true  , '' , false, false );
        $this->addCampo('numero_modalidade'                , 'integer' , true  , '' , false, false );
        $this->addCampo('data_ata_registro_preco'          , 'date'    , true  , '' , false, false );
        $this->addCampo('data_ata_registro_preco_validade' , 'date'    , true  , '' , false, false );
        $this->addCampo('objeto'                           , 'text'    , true  , '' , false, false );
        $this->addCampo('cgm_responsavel'                  , 'integer' , true  , '' , false,  true );
        $this->addCampo('desconto_tabela'                  , 'integer' , true  , '' , false, false );
        $this->addCampo('processo_lote'                    , 'integer' , true  , '' , false, false );
        $this->addCampo('interno'                          , 'boolean' , true  , '' ,  true,  true );
    }

    public function recuperaListaProcesso(&$rsRecordSet)
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaProcesso($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }
    
    public function montaRecuperaListaProcesso()
    {
        $stSql = "
            SELECT cod_entidade
                 , LPAD(numero_registro_precos::VARCHAR, 12, '0') || '/'|| exercicio AS codigo_registro_precos
                 , numero_registro_precos
                 , exercicio
                 , TO_CHAR(data_abertura_registro_precos,'dd/mm/yyyy') AS data_abertura_registro_precos
                 , LPAD(numero_processo_licitacao::VARCHAR, 15, '0') || '/' || exercicio_licitacao AS codigo_processo_licitacao 
                 , CASE WHEN codigo_modalidade_licitacao = 1 THEN 'Concorrência' ELSE 'Pregão' END AS modalidade 
                 , numero_modalidade
                 , CASE WHEN interno IS TRUE THEN 'Interno' ELSE 'Externo' END AS tipo_reg_precos
                 , CASE WHEN interno IS TRUE THEN 'true' ELSE 'false' END AS interno
                 , ( SELECT nom_cgm FROM sw_cgm WHERE numcgm = numcgm_gerenciador ) AS nomcgm_gerenciador
                 , numcgm_gerenciador

              FROM tcemg.registro_precos

             WHERE cod_entidade = ".$this->getDado('cod_entidade');
        if($this->getDado('exercicio')!=NULL)
            $stSql .= " AND exercicio='".$this->getDado('exercicio')."'";

        return $stSql;
    }

   
    public function recuperaProcesso(&$rsRecordSet)
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaProcesso($stFiltro, $stOrdem);
        $this->setDebug($stSQL);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSQL, $boTransacao);

        return $obErro;
    }
    
    public function montaRecuperaProcesso()
    {
        $stSql = "
            SELECT cod_entidade
                 , LPAD(numero_registro_precos::VARCHAR, 12, '0') AS codigo_registro_precos
                 , registro_precos.exercicio AS exercicio_registro_precos
                 , TO_CHAR(data_abertura_registro_precos,'dd/mm/yyyy') AS data_abertura_registro_precos
                 , sw_cgm.numcgm  AS numcgm_gerenciador
                 , sw_cgm.nom_cgm AS nomcgm_gerenciador
                 , numero_processo_licitacao
                 , exercicio_licitacao
                 , codigo_modalidade_licitacao
                 , numero_modalidade
                 , TO_CHAR(data_ata_registro_preco,'dd/mm/yyyy') AS data_ata_registro_preco
                 , TO_CHAR(data_ata_registro_preco_validade,'dd/mm/yyyy') AS data_ata_registro_preco_validade
                 , objeto
                 , sw_cgm_responsavel.numcgm  AS numcgm_responsavel
                 , sw_cgm_responsavel.nom_cgm AS nomcgm_responsavel
                 , desconto_tabela
                 , processo_lote
                 , exercicio
                 , interno
              FROM tcemg.registro_precos
             
        INNER JOIN sw_cgm
                ON sw_cgm.numcgm = registro_precos.numcgm_gerenciador

        INNER JOIN sw_cgm as sw_cgm_responsavel
                ON sw_cgm_responsavel.numcgm = registro_precos.cgm_responsavel

             WHERE registro_precos.exercicio              = '".$this->getDado('exercicio')."'
               AND registro_precos.numero_registro_precos = ".$this->getDado('numero_registro_precos')."
               AND registro_precos.interno                = ".$this->getDado('interno')."
               AND registro_precos.numcgm_gerenciador     = ".$this->getDado('numcgm_gerenciador')."
               AND registro_precos.cod_entidade           = ".$this->getDado('cod_entidade') ;

        return $stSql;
    }


    public function recuperaExportacaoREGADESAO10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO10()
    {
        $stSql = "
        
             SELECT  parp.cod_entidade::VARCHAR||parp.numero_registro_precos::VARCHAR||parp.exercicio::VARCHAR AS chave10
                  ,  10 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  CASE WHEN registro_precos_orgao.numero_registro_precos IS NOT NULL
                                THEN LPAD(LPAD(registro_precos_orgao.num_orgao::VARCHAR, 2, '0')||LPAD(registro_precos_orgao.num_unidade::VARCHAR, 2, '0'),5,'0')
                                ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                     END AS cod_unidade_sub
                  ,  parp.numero_registro_precos  
                  ,  parp.exercicio AS exercicio_adesao
                  ,  TO_CHAR(parp.data_abertura_registro_precos, 'ddmmyyyy') AS data_abertura_processo_adesao
                  ,  CASE WHEN RPO.nome_orgao_gerenciador IS NOT NULL
                                THEN RPO.nome_orgao_gerenciador
                                ELSE sw_cgm.nom_cgm
                     END AS nome_orgao_gerenciador
                  ,  parp.exercicio_licitacao
                  ,  parp.numero_processo_licitacao AS numero_processo_licitacao
                  --MODIFICAR Modalidade Licitação em Cdg Modalidade do Arquivo
                  --No Ticket #23018 foi ajustado, mas deve-se manter o CASE, pois registros de preços mais antigos podem ser das modalidades licitação = 3,6,7.
                  ,  CASE WHEN parp.codigo_modalidade_licitacao = 3 THEN 1
                          WHEN parp.codigo_modalidade_licitacao IN (6,7) THEN 2
			              ELSE parp.codigo_modalidade_licitacao
                     END AS codigo_modalidade_licitacao	
                  ,  parp.numero_modalidade
                  ,  TO_CHAR(homologacao.timestamp,'ddmmyyyy') AS data_homologacao_licitacao
                  ,  TO_CHAR(parp.data_ata_registro_preco, 'ddmmyyyy') AS data_ata_registro_preco
                  ,  TO_CHAR(parp.data_ata_registro_preco_validade, 'ddmmyyyy') AS data_ata_registro_preco_validade
                  ,  CASE WHEN registro_precos_orgao.participante = TRUE THEN 1
                          WHEN registro_precos_orgao.participante = FALSE THEN 2
                     END AS natureza_procedimento
                  ,  TO_CHAR(registro_precos_orgao.dt_publicacao_aviso_intencao, 'ddmmyyyy') AS data_publicacao_aviso_intencao
                  ,  parp.objeto AS objeto_adesao
                  ,  (SELECT cpf FROM sw_cgm_pessoa_fisica WHERE sw_cgm_pessoa_fisica.numcgm = parp.cgm_responsavel) AS cpf_responsavel
                  ,  parp.desconto_tabela
                  ,  parp.processo_lote

              FROM  tcemg.registro_precos AS parp
            
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = parp.numcgm_gerenciador
                
         LEFT JOIN  tcemg.registro_precos_orgao
                ON  registro_precos_orgao.cod_entidade = parp.cod_entidade
               AND  registro_precos_orgao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_orgao.exercicio_registro_precos = parp.exercicio
               AND  registro_precos_orgao.interno = parp.interno
               AND  registro_precos_orgao.numcgm_gerenciador = parp.numcgm_gerenciador
               
         LEFT JOIN  (
                       SELECT orgao.nom_orgao||' - '||unidade.nom_unidade AS nome_orgao_gerenciador
                            , RPO.*
                         FROM tcemg.registro_precos_orgao AS RPO
                       INNER JOIN orcamento.orgao
                           ON orgao.num_orgao = RPO.num_orgao
                          AND orgao.exercicio = RPO.exercicio_unidade
           
                       INNER JOIN orcamento.unidade
                           ON unidade.num_orgao = RPO.num_orgao
                          AND unidade.num_unidade = RPO.num_unidade
                          AND unidade.exercicio = RPO.exercicio_unidade
                        WHERE RPO.gerenciador = TRUE
                 )  AS RPO
                ON  RPO.cod_entidade = parp.cod_entidade
               AND  RPO.numero_registro_precos = parp.numero_registro_precos
               AND  RPO.exercicio_registro_precos = parp.exercicio
               AND  RPO.interno = parp.interno
               AND  RPO.numcgm_gerenciador = parp.numcgm_gerenciador   
               
        INNER JOIN  tcemg.registro_precos_licitacao
                ON  registro_precos_licitacao.cod_entidade = parp.cod_entidade
               AND  registro_precos_licitacao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_licitacao.exercicio = parp.exercicio
               AND  registro_precos_licitacao.interno = parp.interno
               
        INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 )  config_licitacao
                ON  config_licitacao.cod_entidade = registro_precos_licitacao.cod_entidade_licitacao
               AND  config_licitacao.cod_licitacao = registro_precos_licitacao.cod_licitacao
               AND  config_licitacao.cod_modalidade = registro_precos_licitacao.cod_modalidade
               AND  config_licitacao.exercicio = registro_precos_licitacao.exercicio_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                 )  AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                              AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                              AND homologacao.cod_item                    = homologacao_anulada.cod_item
                    ) IS NULL
            
             WHERE  1=1 ";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") "; 
        }

        
        $stSql .= " AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes_referencia') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                      AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes_referencia') . "' || '-' || '01','yyyy-mm-dd')) ";
        
        return $stSql;
    }

    public function recuperaExportacaoREGADESAO11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    

        if ($stOrdem == "")
            $stOrdem = 'ORDER BY chave11, cod_orgao, cod_unidade_sub, parp.numero_registro_precos, parp.exercicio, lrp.cod_lote';
            
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO11()
    {
        $stSql = "
        
             SELECT  lrp.cod_entidade::VARCHAR||lrp.numero_registro_precos::VARCHAR||lrp.exercicio::VARCHAR AS chave11
                  ,  11 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  CASE WHEN registro_precos_orgao.numero_registro_precos IS NOT NULL
                                THEN LPAD(LPAD(registro_precos_orgao.num_orgao::VARCHAR, 2, '0')||LPAD(registro_precos_orgao.num_unidade::VARCHAR, 2, '0'),5,'0')
                                ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                     END AS cod_unidade_sub
                  ,  parp.numero_registro_precos  
                  ,  parp.exercicio AS exercicio_adesao
                  ,  lrp.cod_lote
                  ,  lrp.descricao_lote

              FROM  tcemg.registro_precos AS parp
            
        INNER JOIN  tcemg.lote_registro_precos lrp
                ON  lrp.cod_entidade = parp.cod_entidade
               AND  lrp.numero_registro_precos = parp.numero_registro_precos
               AND  lrp.exercicio = parp.exercicio
               
         LEFT JOIN  tcemg.registro_precos_orgao
                ON  registro_precos_orgao.cod_entidade = parp.cod_entidade
               AND  registro_precos_orgao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_orgao.exercicio_registro_precos = parp.exercicio
               AND  registro_precos_orgao.interno = parp.interno
               AND  registro_precos_orgao.numcgm_gerenciador = parp.numcgm_gerenciador
               
        INNER JOIN  tcemg.registro_precos_licitacao
                ON  registro_precos_licitacao.cod_entidade = parp.cod_entidade
               AND  registro_precos_licitacao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_licitacao.exercicio = parp.exercicio
               AND  registro_precos_licitacao.interno = parp.interno               
               
        INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 )  config_licitacao
                ON  config_licitacao.cod_entidade = registro_precos_licitacao.cod_entidade_licitacao
               AND  config_licitacao.cod_licitacao = registro_precos_licitacao.cod_licitacao
               AND  config_licitacao.cod_modalidade = registro_precos_licitacao.cod_modalidade
               AND  config_licitacao.exercicio = registro_precos_licitacao.exercicio_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                 )  AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                              AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                              AND homologacao.cod_item                    = homologacao_anulada.cod_item
                    ) IS NULL
            
             WHERE  1=1 \n";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") \n"; 
        }

        
        $stSql .= " AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy')
                        BETWEEN TO_DATE('01/" . $this->getDado('mes_referencia') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes_referencia') . "' || '-' || '01','yyyy-mm-dd')) \n";

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO12(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if ($stOrdem == "")
            $stOrdem = 'ORDER BY chave12, cod_orgao, cod_unidade_sub, parp.numero_registro_precos, parp.exercicio, irp.cod_lote, irp.num_item';
            
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO12()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_registro_precos::VARCHAR||irp.exercicio::VARCHAR AS chave12
                  ,  12 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  CASE WHEN registro_precos_orgao.numero_registro_precos IS NOT NULL
                                THEN LPAD(LPAD(registro_precos_orgao.num_orgao::VARCHAR, 2, '0')||LPAD(registro_precos_orgao.num_unidade::VARCHAR, 2, '0'),5,'0')
                                ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                     END AS cod_unidade_sub
                  ,  parp.numero_registro_precos  
                  ,  parp.exercicio AS exercicio_adesao
                  ,  irp.cod_item
                  ,  irp.num_item

              FROM  tcemg.registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_registro_precos = parp.numero_registro_precos
               AND  irp.exercicio = parp.exercicio
               
        INNER JOIN  tcemg.registro_precos_orgao
                ON  registro_precos_orgao.cod_entidade = parp.cod_entidade
               AND  registro_precos_orgao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_orgao.exercicio_registro_precos = parp.exercicio
               AND  registro_precos_orgao.interno = parp.interno
               AND  registro_precos_orgao.numcgm_gerenciador = parp.numcgm_gerenciador
               
        INNER JOIN  tcemg.registro_precos_orgao_item
                ON  registro_precos_orgao_item.cod_entidade = registro_precos_orgao.cod_entidade
               AND  registro_precos_orgao_item.numero_registro_precos = registro_precos_orgao.numero_registro_precos
               AND  registro_precos_orgao_item.exercicio_registro_precos = registro_precos_orgao.exercicio_registro_precos
               AND  registro_precos_orgao_item.interno = registro_precos_orgao.interno
               AND  registro_precos_orgao_item.numcgm_gerenciador = registro_precos_orgao.numcgm_gerenciador
               AND  registro_precos_orgao_item.exercicio_unidade = registro_precos_orgao.exercicio_unidade
               AND  registro_precos_orgao_item.num_unidade = registro_precos_orgao.num_unidade
               AND  registro_precos_orgao_item.num_orgao = registro_precos_orgao.num_orgao 
               AND  registro_precos_orgao_item.cod_lote = irp.cod_lote
               AND  registro_precos_orgao_item.cod_item = irp.cod_item
               AND  registro_precos_orgao_item.cgm_fornecedor = irp.cgm_fornecedor

        INNER JOIN  tcemg.registro_precos_licitacao
                ON  registro_precos_licitacao.cod_entidade = parp.cod_entidade
               AND  registro_precos_licitacao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_licitacao.exercicio = parp.exercicio
               AND  registro_precos_licitacao.interno = parp.interno 
               
        INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 )  config_licitacao
                ON  config_licitacao.cod_entidade = registro_precos_licitacao.cod_entidade_licitacao
               AND  config_licitacao.cod_licitacao = registro_precos_licitacao.cod_licitacao
               AND  config_licitacao.cod_modalidade = registro_precos_licitacao.cod_modalidade
               AND  config_licitacao.exercicio = registro_precos_licitacao.exercicio_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                 )  AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                              AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                              AND homologacao.cod_item                    = homologacao_anulada.cod_item
                    ) IS NULL
            
             WHERE  1=1 \n";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") \n"; 
        }

        
        $stSql .= " AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy')
                        BETWEEN TO_DATE('01/" . $this->getDado('mes_referencia') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes_referencia') . "' || '-' || '01','yyyy-mm-dd')) \n";
        
        $stSql .= " GROUP BY 1,2,3,4,5,6,7,8,irp.cod_lote \n";

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO13(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if ($stOrdem == "")
            $stOrdem = 'ORDER BY chave13, cod_orgao, cod_unidade_sub, parp.numero_registro_precos, parp.exercicio, irp.cod_lote, irp.num_item';
        
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO13().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO13()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_registro_precos::VARCHAR||irp.exercicio::VARCHAR AS chave13
                  ,  13 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  CASE WHEN registro_precos_orgao.numero_registro_precos IS NOT NULL
                                THEN LPAD(LPAD(registro_precos_orgao.num_orgao::VARCHAR, 2, '0')||LPAD(registro_precos_orgao.num_unidade::VARCHAR, 2, '0'),5,'0')
                                ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                     END AS cod_unidade_sub
                  ,  parp.numero_registro_precos  
                  ,  parp.exercicio AS exercicio_adesao
                  ,  irp.cod_item
                  ,  irp.cod_lote

              FROM  tcemg.registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_registro_precos = parp.numero_registro_precos
               AND  irp.exercicio = parp.exercicio
             
        INNER JOIN  tcemg.registro_precos_orgao
                ON  registro_precos_orgao.cod_entidade = parp.cod_entidade
               AND  registro_precos_orgao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_orgao.exercicio_registro_precos = parp.exercicio
               AND  registro_precos_orgao.interno = parp.interno
               AND  registro_precos_orgao.numcgm_gerenciador = parp.numcgm_gerenciador
               
        INNER JOIN  tcemg.registro_precos_orgao_item
                ON  registro_precos_orgao_item.cod_entidade = registro_precos_orgao.cod_entidade
               AND  registro_precos_orgao_item.numero_registro_precos = registro_precos_orgao.numero_registro_precos
               AND  registro_precos_orgao_item.exercicio_registro_precos = registro_precos_orgao.exercicio_registro_precos
               AND  registro_precos_orgao_item.interno = registro_precos_orgao.interno
               AND  registro_precos_orgao_item.numcgm_gerenciador = registro_precos_orgao.numcgm_gerenciador
               AND  registro_precos_orgao_item.exercicio_unidade = registro_precos_orgao.exercicio_unidade
               AND  registro_precos_orgao_item.num_unidade = registro_precos_orgao.num_unidade
               AND  registro_precos_orgao_item.num_orgao = registro_precos_orgao.num_orgao 
               AND  registro_precos_orgao_item.cod_lote = irp.cod_lote
               AND  registro_precos_orgao_item.cod_item = irp.cod_item
               AND  registro_precos_orgao_item.cgm_fornecedor = irp.cgm_fornecedor

        INNER JOIN  tcemg.registro_precos_licitacao
                ON  registro_precos_licitacao.cod_entidade = parp.cod_entidade
               AND  registro_precos_licitacao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_licitacao.exercicio = parp.exercicio
               AND  registro_precos_licitacao.interno = parp.interno 
               
        INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 )  config_licitacao
                ON  config_licitacao.cod_entidade = registro_precos_licitacao.cod_entidade_licitacao
               AND  config_licitacao.cod_licitacao = registro_precos_licitacao.cod_licitacao
               AND  config_licitacao.cod_modalidade = registro_precos_licitacao.cod_modalidade
               AND  config_licitacao.exercicio = registro_precos_licitacao.exercicio_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                 )  AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                              AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                              AND homologacao.cod_item                    = homologacao_anulada.cod_item
                    ) IS NULL
            
             WHERE  1=1 \n";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") \n"; 
        }

        $stSql .= " AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy')
                        BETWEEN TO_DATE('01/" . $this->getDado('mes_referencia') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes_referencia') . "' || '-' || '01','yyyy-mm-dd')) \n";
        
        $stSql .= " GROUP BY 1,2,3,4,5,6,7,8,irp.num_item \n";

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO14(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if ($stOrdem == "")
            $stOrdem = 'ORDER BY chave14, cod_orgao, cod_unidade_sub, parp.numero_registro_precos, parp.exercicio, irp.cod_lote, irp.num_item';
            
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO14().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO14()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_registro_precos::VARCHAR||irp.exercicio::VARCHAR AS chave14
                  ,  14 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  CASE WHEN registro_precos_orgao.numero_registro_precos IS NOT NULL
                                THEN LPAD(LPAD(registro_precos_orgao.num_orgao::VARCHAR, 2, '0')||LPAD(registro_precos_orgao.num_unidade::VARCHAR, 2, '0'),5,'0')
                                ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                     END AS cod_unidade_sub
                  ,  parp.numero_registro_precos  
                  ,  parp.exercicio AS exercicio_adesao
                  ,  irp.cod_lote
                  ,  irp.cod_item
                  ,  TO_CHAR(irp.data_cotacao,'ddmmyyyy') AS data_cotacao
                  ,  REPLACE(irp.vl_cotacao_preco_unitario::VARCHAR,'.',',') AS vl_cotacao_preco_unitario
                  ,  REPLACE(irp.quantidade_cotacao::VARCHAR,'.',',') AS quantidade_cotacao
                  
              FROM  tcemg.registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_registro_precos = parp.numero_registro_precos
               AND  irp.exercicio = parp.exercicio
             
        INNER JOIN  tcemg.registro_precos_orgao
                ON  registro_precos_orgao.cod_entidade = parp.cod_entidade
               AND  registro_precos_orgao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_orgao.exercicio_registro_precos = parp.exercicio
               AND  registro_precos_orgao.interno = parp.interno
               AND  registro_precos_orgao.numcgm_gerenciador = parp.numcgm_gerenciador
               
        INNER JOIN  tcemg.registro_precos_orgao_item
                ON  registro_precos_orgao_item.cod_entidade = registro_precos_orgao.cod_entidade
               AND  registro_precos_orgao_item.numero_registro_precos = registro_precos_orgao.numero_registro_precos
               AND  registro_precos_orgao_item.exercicio_registro_precos = registro_precos_orgao.exercicio_registro_precos
               AND  registro_precos_orgao_item.interno = registro_precos_orgao.interno
               AND  registro_precos_orgao_item.numcgm_gerenciador = registro_precos_orgao.numcgm_gerenciador
               AND  registro_precos_orgao_item.exercicio_unidade = registro_precos_orgao.exercicio_unidade
               AND  registro_precos_orgao_item.num_unidade = registro_precos_orgao.num_unidade
               AND  registro_precos_orgao_item.num_orgao = registro_precos_orgao.num_orgao 
               AND  registro_precos_orgao_item.cod_lote = irp.cod_lote
               AND  registro_precos_orgao_item.cod_item = irp.cod_item
               AND  registro_precos_orgao_item.cgm_fornecedor = irp.cgm_fornecedor

        INNER JOIN  tcemg.registro_precos_licitacao
                ON  registro_precos_licitacao.cod_entidade = parp.cod_entidade
               AND  registro_precos_licitacao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_licitacao.exercicio = parp.exercicio
               AND  registro_precos_licitacao.interno = parp.interno
               
        INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 )  config_licitacao
                ON  config_licitacao.cod_entidade = registro_precos_licitacao.cod_entidade_licitacao
               AND  config_licitacao.cod_licitacao = registro_precos_licitacao.cod_licitacao
               AND  config_licitacao.cod_modalidade = registro_precos_licitacao.cod_modalidade
               AND  config_licitacao.exercicio = registro_precos_licitacao.exercicio_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                          ) AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                              AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                              AND homologacao.cod_item                    = homologacao_anulada.cod_item
                    ) IS NULL
            
             WHERE  1=1 \n";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") \n"; 
        }

        
        $stSql .= " AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy')
                        BETWEEN TO_DATE('01/" . $this->getDado('mes_referencia') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes_referencia') . "' || '-' || '01','yyyy-mm-dd')) \n";

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO15(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    

        if ($stOrdem == "")
            $stOrdem = 'ORDER BY chave15, cod_orgao, cod_unidade_sub, parp.numero_registro_precos, parp.exercicio, irp.cod_lote, irp.num_item';
        
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO15().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO15()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_registro_precos::VARCHAR||irp.exercicio::VARCHAR AS chave15
                  ,  15 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  CASE WHEN registro_precos_orgao.numero_registro_precos IS NOT NULL
                                THEN LPAD(LPAD(registro_precos_orgao.num_orgao::VARCHAR, 2, '0')||LPAD(registro_precos_orgao.num_unidade::VARCHAR, 2, '0'),5,'0')
                                ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                     END AS cod_unidade_sub
                  ,  parp.numero_registro_precos  
                  ,  parp.exercicio AS exercicio_adesao
                  ,  irp.cod_lote
                  ,  irp.cod_item
                  ,  REPLACE(irp.preco_unitario::VARCHAR,'.',',') AS preco_unitario
                  ,  REPLACE(irp.quantidade_licitada::VARCHAR,'.',',') AS quantidade_licitada
                  ,  REPLACE(irp.quantidade_aderida::VARCHAR,'.',',') AS quantidade_aderida
                  ,  CASE WHEN sw_cgm.cod_pais <> 1 THEN 3
                          WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                          ELSE 2 END AS tipo_documento                 
                  ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf                        
                          ELSE sw_cgm_pessoa_juridica.cnpj END AS nro_documento
                  
              FROM  tcemg.registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_registro_precos = parp.numero_registro_precos
               AND  irp.exercicio = parp.exercicio
             
        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = irp.cgm_fornecedor
        
         LEFT JOIN  sw_cgm_pessoa_fisica   
                ON  sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm               

         LEFT JOIN  sw_cgm_pessoa_juridica
                ON  sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

        INNER JOIN  tcemg.registro_precos_orgao
                ON  registro_precos_orgao.cod_entidade = parp.cod_entidade
               AND  registro_precos_orgao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_orgao.exercicio_registro_precos = parp.exercicio
               AND  registro_precos_orgao.interno = parp.interno
               AND  registro_precos_orgao.numcgm_gerenciador = parp.numcgm_gerenciador
               
        INNER JOIN  tcemg.registro_precos_orgao_item
                ON  registro_precos_orgao_item.cod_entidade = registro_precos_orgao.cod_entidade
               AND  registro_precos_orgao_item.numero_registro_precos = registro_precos_orgao.numero_registro_precos
               AND  registro_precos_orgao_item.exercicio_registro_precos = registro_precos_orgao.exercicio_registro_precos
               AND  registro_precos_orgao_item.interno = registro_precos_orgao.interno
               AND  registro_precos_orgao_item.numcgm_gerenciador = registro_precos_orgao.numcgm_gerenciador
               AND  registro_precos_orgao_item.exercicio_unidade = registro_precos_orgao.exercicio_unidade
               AND  registro_precos_orgao_item.num_unidade = registro_precos_orgao.num_unidade
               AND  registro_precos_orgao_item.num_orgao = registro_precos_orgao.num_orgao 
               AND  registro_precos_orgao_item.cod_lote = irp.cod_lote
               AND  registro_precos_orgao_item.cod_item = irp.cod_item
               AND  registro_precos_orgao_item.cgm_fornecedor = irp.cgm_fornecedor

        INNER JOIN  tcemg.registro_precos_licitacao
                ON  registro_precos_licitacao.cod_entidade = parp.cod_entidade
               AND  registro_precos_licitacao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_licitacao.exercicio = parp.exercicio
               AND  registro_precos_licitacao.interno = parp.interno
               
        INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 )  config_licitacao
                ON  config_licitacao.cod_entidade = registro_precos_licitacao.cod_entidade_licitacao
               AND  config_licitacao.cod_licitacao = registro_precos_licitacao.cod_licitacao
               AND  config_licitacao.cod_modalidade = registro_precos_licitacao.cod_modalidade
               AND  config_licitacao.exercicio = registro_precos_licitacao.exercicio_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                 )  AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                              AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                              AND homologacao.cod_item                    = homologacao_anulada.cod_item
                    ) IS NULL
            
             WHERE  1=1 \n";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") \n"; 
        }

        
        $stSql .= " AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy')
                        BETWEEN TO_DATE('01/" . $this->getDado('mes_referencia') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes_referencia') . "' || '-' || '01','yyyy-mm-dd')) \n";

        return $stSql;
    }

    public function recuperaExportacaoREGADESAO20(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if ($stOrdem == "")
            $stOrdem = 'ORDER BY chave20, cod_orgao, cod_unidade_sub, parp.numero_registro_precos, parp.exercicio, irp.cod_lote, irp.num_item';
        
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaExportacaoREGADESAO20().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaExportacaoREGADESAO20()
    {
        $stSql = "
        
             SELECT  irp.cod_entidade::VARCHAR||irp.numero_registro_precos::VARCHAR||irp.exercicio::VARCHAR AS chave20
                  ,  20 AS tipo_registro
                  ,  (SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = parp.exercicio AND parametro = 'tcemg_codigo_orgao_entidade_sicom' AND cod_entidade = parp.cod_entidade) AS cod_orgao
                  ,  CASE WHEN registro_precos_orgao.numero_registro_precos IS NOT NULL
                                THEN LPAD(LPAD(registro_precos_orgao.num_orgao::VARCHAR, 2, '0')||LPAD(registro_precos_orgao.num_unidade::VARCHAR, 2, '0'),5,'0')
                                ELSE LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0')
                     END AS cod_unidade_sub
                  ,  parp.numero_registro_precos  
                  ,  parp.exercicio AS exercicio_adesao
                  ,  irp.cod_lote
                  ,  irp.cod_item
                  ,  irp.percentual_desconto AS percentual_desconto_item
                  ,  lrp.percentual_desconto_lote AS percentual_desconto_lote
                  ,  CASE WHEN sw_cgm.cod_pais <> 1 THEN 3
                          WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                          ELSE 2 END AS tipo_documento                 
                  ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf                        
                          ELSE sw_cgm_pessoa_juridica.cnpj END AS nro_documento
                  
              FROM  tcemg.registro_precos AS parp
            
        INNER JOIN  tcemg.item_registro_precos irp
                ON  irp.cod_entidade = parp.cod_entidade
               AND  irp.numero_registro_precos = parp.numero_registro_precos
               AND  irp.exercicio = parp.exercicio

         LEFT JOIN  tcemg.lote_registro_precos lrp
                ON  lrp.cod_entidade = irp.cod_entidade
               AND  lrp.numero_registro_precos = irp.numero_registro_precos
               AND  lrp.exercicio = irp.exercicio
               AND  lrp.cod_lote = irp.cod_lote

        INNER JOIN  sw_cgm
                ON  sw_cgm.numcgm = irp.cgm_fornecedor
        
         LEFT JOIN  sw_cgm_pessoa_fisica   
                ON  sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm               

         LEFT JOIN  sw_cgm_pessoa_juridica
                ON  sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

        INNER JOIN  tcemg.registro_precos_orgao
                ON  registro_precos_orgao.cod_entidade = parp.cod_entidade
               AND  registro_precos_orgao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_orgao.exercicio_registro_precos = parp.exercicio
               AND  registro_precos_orgao.interno = parp.interno
               AND  registro_precos_orgao.numcgm_gerenciador = parp.numcgm_gerenciador
               
        INNER JOIN  tcemg.registro_precos_orgao_item
                ON  registro_precos_orgao_item.cod_entidade = registro_precos_orgao.cod_entidade
               AND  registro_precos_orgao_item.numero_registro_precos = registro_precos_orgao.numero_registro_precos
               AND  registro_precos_orgao_item.exercicio_registro_precos = registro_precos_orgao.exercicio_registro_precos
               AND  registro_precos_orgao_item.interno = registro_precos_orgao.interno
               AND  registro_precos_orgao_item.numcgm_gerenciador = registro_precos_orgao.numcgm_gerenciador
               AND  registro_precos_orgao_item.exercicio_unidade = registro_precos_orgao.exercicio_unidade
               AND  registro_precos_orgao_item.num_unidade = registro_precos_orgao.num_unidade
               AND  registro_precos_orgao_item.num_orgao = registro_precos_orgao.num_orgao 
               AND  registro_precos_orgao_item.cod_lote = irp.cod_lote
               AND  registro_precos_orgao_item.cod_item = irp.cod_item
               AND  registro_precos_orgao_item.cgm_fornecedor = irp.cgm_fornecedor

        INNER JOIN  tcemg.registro_precos_licitacao
                ON  registro_precos_licitacao.cod_entidade = parp.cod_entidade
               AND  registro_precos_licitacao.numero_registro_precos = parp.numero_registro_precos
               AND  registro_precos_licitacao.exercicio = parp.exercicio
               AND  registro_precos_licitacao.interno = parp.interno
               
        INNER JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 )  config_licitacao
                ON  config_licitacao.cod_entidade = registro_precos_licitacao.cod_entidade_licitacao
               AND  config_licitacao.cod_licitacao = registro_precos_licitacao.cod_licitacao
               AND  config_licitacao.cod_modalidade = registro_precos_licitacao.cod_modalidade
               AND  config_licitacao.exercicio = registro_precos_licitacao.exercicio_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                 )  AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
                              AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
                              AND homologacao.cod_item                    = homologacao_anulada.cod_item
                    ) IS NULL
            
             WHERE  1=1 \n";
             
        if ($this->getDado('entidades')) {
            $stSql .= " AND parp.cod_entidade IN (".$this->getDado('entidades').") \n"; 
        }

        
        $stSql .= " AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy')
                        BETWEEN TO_DATE('01/" . $this->getDado('mes_referencia') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes_referencia') . "' || '-' || '01','yyyy-mm-dd')) \n";

        return $stSql;
    }

    public function __destruct(){}
        
}

?>