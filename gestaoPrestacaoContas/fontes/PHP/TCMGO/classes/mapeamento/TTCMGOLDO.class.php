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
    * Classe de mapeamento da tabela TTCMGOLDO
    * Data de Criação: 09/02/2015

    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Evandro Melos
    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOLDO extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOLDO()
    {
        parent::Persistente();
    }

    public function recuperaArquivoExportacao10(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
        $stSql = $this->montaRecuperaArquivoExportacao10();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }

    public function montaRecuperaArquivoExportacao10()
    {
        $stSql = "  SELECT
                            '10' as tipo_registro
                            , norma.num_norma AS nro_ldo
                            , TO_CHAR(norma.dt_assinatura, 'DDMMYYYY') AS data_ldo
                            , '' AS brancos                           
                    FROM tcmgo.configuracao_leis_ldo

                    JOIN normas.norma
                        ON norma.cod_norma = configuracao_leis_ldo.cod_norma

                    WHERE configuracao_leis_ldo.exercicio = '".$this->getDado('exercicio')."'
                    AND configuracao_leis_ldo.status <> 'f'
                    AND configuracao_leis_ldo.tipo_configuracao = 'consulta'
        ";
        return $stSql;
    }

    public function recuperaArquivoExportacao11(&$rsRecordSet,$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
        $stSql = $this->montaRecuperaArquivoExportacao11();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }

    public function montaRecuperaArquivoExportacao11()
    {
        $stSql = "  SELECT DISTINCT
                        11 as tipo_registro
                        ,norma.num_norma as nro_ldo
                        , TO_CHAR(norma.dt_assinatura, 'DDMMYYYY') AS data_ldo
                        ,CASE tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade
                            WHEN 6 THEN 1
                            WHEN 1 THEN 4
                            WHEN 2 THEN 4
                            WHEN 3 THEN 4
                            WHEN 4 THEN 4
                            WHEN 5 THEN 5
                            WHEN 7 THEN 5
                            WHEN 8 THEN 3
                            WHEN 9 THEN 9
                        END as meio_pub_ldo
                        ,tipo_veiculos_publicidade.descricao as desc_meio_ldo
                        ,TO_CHAR(norma.dt_publicacao,'ddmmyyyy') as data_pub_lei_ldo                        
                    FROM tcmgo.configuracao_leis_ldo
                    JOIN normas.norma
                        ON configuracao_leis_ldo.cod_norma = norma.cod_norma                    
                    JOIN ldo.homologacao
                        ON homologacao.cod_norma = norma.cod_norma                    
                    JOIN licitacao.veiculos_publicidade
                        ON veiculos_publicidade.numcgm = homologacao.numcgm_veiculo
                    JOIN licitacao.tipo_veiculos_publicidade
                        ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                    WHERE configuracao_leis_ldo.tipo_configuracao = 'consulta'
                    AND configuracao_leis_ldo.exercicio = '".$this->getDado('exercicio')."'
        ";
        return $stSql;        
    }

    public function recuperaArquivoExportacao20(&$rsRecordSet,$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
        $stSql = $this->montaRecuperaArquivoExportacao20();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }

    public function montaRecuperaArquivoExportacao20()
    {
        $stSql = "  SELECT 
                            '20'  as tipo_registro
                            , metas_fiscais_ldo.valor_corrente_receita as meta_rec
                            , metas_fiscais_ldo.valor_corrente_despesa as meta_desp
                            , metas_fiscais_ldo.valor_corrente_resultado_primario as meta_rp
                            , metas_fiscais_ldo.valor_corrente_resultado_nominal as meta_rn
                            , metas_fiscais_ldo.valor_corrente_divida_consolidada_liquida as meta_dcl
                            , exercicio
                            , '' as brancos
                    FROM tcmgo.metas_fiscais_ldo
                    WHERE exercicio  = '".$this->getDado('exercicio')."'                    
                ";
        return $stSql;
    }

    public function recuperaArquivoExportacao21(&$rsRecordSet,$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
        $stSql = $this->montaRecuperaArquivoExportacao21();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }

    public function montaRecuperaArquivoExportacao21()
    {
        $stSql = "  SELECT 
                            '21' as tipo_registro
                            , meta_arrecadacao_1_bi as meta_arrec_1_bim
                            , meta_arrecadacao_2_bi as meta_arrec_2_bim
                            , meta_arrecadacao_3_bi as meta_arrec_3_bim
                            , meta_arrecadacao_4_bi as meta_arrec_4_bim
                            , meta_arrecadacao_5_bi as meta_arrec_5_bim
                            , meta_arrecadacao_6_bi as meta_arrec_6_bim
                            , exercicio
                            , '' as brancos
                    FROM tcmgo.metas_arrecadacao_receita
                    WHERE exercicio = '".$this->getDado('exercicio')."'
                ";
        return $stSql;
    }



}
?>