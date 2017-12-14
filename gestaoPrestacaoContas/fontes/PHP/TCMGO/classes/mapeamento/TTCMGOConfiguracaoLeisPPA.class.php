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
    * Classe de mapeamento da tabela TCEMG.CONFIGURACAO_LEIS_PPA
    * Data de Criação: 14/01/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: TTCMGOConfiguracaoLeisPPA.class.php 61672 2015-02-24 14:21:04Z michel $
    *
    * $Name: $
    * $Date: 2015-02-24 11:21:04 -0300 (Tue, 24 Feb 2015) $
    * $Author: michel $
    * $Rev: 61672 $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCMGOConfiguracaoLeisPPA extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOConfiguracaoLeisPPA()
    {
        parent::Persistente();
        $this->setTabela('tcmgo.configuracao_leis_ppa');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_norma, tipo_configuracao');

        $this->AddCampo('exercicio'             , 'varchar', true, '4',  true, false);
        $this->AddCampo('cod_norma'             , 'integer', true,  '',  true,  true);
        $this->AddCampo('tipo_configuracao'     , 'varchar', true,  '', false, false);
        $this->AddCampo('status'                , 'boolean', true,  '', false, false);
        $this->AddCampo('cod_veiculo_publicacao', 'integer', true,  '', false,  true);
        $this->AddCampo('descricao_publicacao'  , 'text'   , false, '', false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql .= "SELECT configuracao_leis_ppa.*                                           \n";
        $stSql .= "     , norma.cod_tipo_norma                                              \n";
        $stSql .= "     , norma.nom_norma                                                   \n";
        $stSql .= "     , tipo_norma.nom_tipo_norma                                         \n";
        $stSql .= "  FROM tcmgo.configuracao_leis_ppa                                       \n";
        $stSql .= "     , normas.norma                                                      \n";
        $stSql .= "     , normas.tipo_norma                                                 \n";
        $stSql .= " WHERE configuracao_leis_ppa.cod_norma = norma.cod_norma                 \n";
        $stSql .= "   AND norma.cod_tipo_norma = tipo_norma.cod_tipo_norma                  \n";

        return $stSql;
    }


    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
        $stSql = "  SELECT DISTINCT  
                            10 as tipo_registro
                            ,norma.num_norma as nro_lei_ppa
                            ,TO_CHAR(norma.dt_assinatura,'ddmmyyyy') as data_lei_ppa
                            ,'' as brancos
                            ,TO_CHAR(norma.dt_publicacao,'ddmmyyyy') as data_pub_lei_ppa
                       FROM ppa.ppa       					                
                       JOIN ppa.ppa_publicacao							    
                         ON ppa_publicacao.cod_ppa = ppa.cod_ppa
                        AND ppa_publicacao.timestamp = (SELECT MAX(ppa_publicacao.timestamp) FROM ppa.ppa_publicacao WHERE ppa_publicacao.cod_ppa = ppa.cod_ppa)
                        AND ".Sessao::getExercicio()." BETWEEN ppa.ano_inicio::INTEGER AND ppa.ano_final::INTEGER
                       JOIN normas.norma
                         ON norma.cod_norma = ppa_publicacao.cod_norma
                       JOIN normas.tipo_norma
                         ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma
                      WHERE ppa.fn_verifica_homologacao(ppa.cod_ppa) = TRUE
        ";
        return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = "  SELECT DISTINCT
                            11 as tipo_registro
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
                            END AS meio_pub_ppa
                            ,tipo_veiculos_publicidade.descricao as desc_meio_ppa
                            ,TO_CHAR(norma.dt_publicacao,'ddmmyyyy') as data_pub_lei_ppa
                            ,norma.num_norma as nro_lei_ppa
                            ,TO_CHAR(norma.dt_assinatura,'ddmmyyyy') as data_lei_ppa
                       FROM ppa.ppa       					                
                       JOIN ppa.ppa_publicacao							    
                         ON ppa_publicacao.cod_ppa = ppa.cod_ppa
                        AND ppa_publicacao.timestamp = (SELECT MAX(ppa_publicacao.timestamp) FROM ppa.ppa_publicacao WHERE ppa_publicacao.cod_ppa = ppa.cod_ppa)
                        AND ".Sessao::getExercicio()." BETWEEN ppa.ano_inicio::INTEGER AND ppa.ano_final::INTEGER
                       JOIN normas.norma
                         ON norma.cod_norma = ppa_publicacao.cod_norma
                       JOIN normas.tipo_norma
                         ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma
                       JOIN licitacao.veiculos_publicidade
                         ON veiculos_publicidade.numcgm = ppa_publicacao.numcgm_veiculo
                       JOIN licitacao.tipo_veiculos_publicidade
                         ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                      WHERE ppa.fn_verifica_homologacao(ppa.cod_ppa) = TRUE
        ";
        return $stSql;        
    }
    
    public function recuperaExportacao20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao20()
    {
        $stSql = "  SELECT DISTINCT  
                        20 AS tipo_registro
                        ,norma.num_norma AS nro_lei_alt_ppa
                        ,TO_CHAR(norma.dt_assinatura,'ddmmyyyy') AS data_lei_alt_ppa
                        ,'' AS brancos                            
                    FROM tcmgo.configuracao_leis_ppa
                    JOIN normas.norma
                      ON configuracao_leis_ppa.cod_norma = norma.cod_norma                    
                   WHERE configuracao_leis_ppa.tipo_configuracao = 'alteracao'
                    AND configuracao_leis_ppa.status = true
                    AND configuracao_leis_ppa.exercicio = '".Sessao::getExercicio()."'
        ";
        return $stSql;        
    }
    
    public function recuperaExportacao21(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao21",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao21()
    {
        $stSql = " SELECT DISTINCT
                        21 as tipo_registro
                        ,configuracao_leis_ppa.cod_veiculo_publicacao AS meio_pub_alt_ppa
                        ,configuracao_leis_ppa.descricao_publicacao AS desc_meio_alt_ppa
                        ,TO_CHAR(norma.dt_publicacao,'ddmmyyyy') AS data_pub_lei_alt_ppa
                        ,norma.num_norma AS nro_lei_alt_ppa
                        ,TO_CHAR(norma.dt_assinatura,'ddmmyyyy') AS data_lei_alt_ppa
                    FROM tcmgo.configuracao_leis_ppa
                    JOIN normas.norma
                      ON configuracao_leis_ppa.cod_norma = norma.cod_norma
                   WHERE configuracao_leis_ppa.tipo_configuracao = 'alteracao'
                     AND configuracao_leis_ppa.status = true
                     AND configuracao_leis_ppa.exercicio = '".Sessao::getExercicio()."'
        ";
        return $stSql;        
    }

    /**
        * Método Destrutor
        * @access Private
    */
    public function __destruct()
    {
    }
}
?>
