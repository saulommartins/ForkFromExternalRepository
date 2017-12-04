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
    * Classe de mapeamento do Relatorio Domicio Fiscal
    * Data de Criação: 09/09/2014
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TRelatorioDomicilioFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TRelatorioDomicilioFiscal()
    {
        parent::Persistente();

    }

    function recuperaRelatorioDomicilioFiscal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelatorioDomicilioFiscal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaRelatorioDomicilioFiscal()
    {
        $stSql = "SELECT DISTINCT 
                            cadastro_economico.inscricao_economica
                            ,sw_cgm.nom_cgm
                            ,sw_nome_logradouro.nom_logradouro
                            ,domicilio_fiscal.inscricao_municipal
                        FROM economico.cadastro_economico
                        JOIN economico.atividade_cadastro_economico
                            ON atividade_cadastro_economico.inscricao_economica = cadastro_economico.inscricao_economica
                        JOIN economico.atividade
                            ON atividade.cod_atividade = atividade_cadastro_economico.cod_atividade
                        JOIN economico.domicilio_fiscal
                            ON domicilio_fiscal.inscricao_economica = cadastro_economico.inscricao_economica
                        JOIN economico.domicilio_informado
                            ON domicilio_informado.inscricao_economica = cadastro_economico.inscricao_economica
                        JOIN sw_logradouro
                            ON sw_logradouro.cod_logradouro = domicilio_informado.cod_logradouro
                        JOIN sw_nome_logradouro
                            ON sw_nome_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
                        --Buscar todos dos numcgm de empresas fato, autonomo e direito
                        JOIN ( SELECT 
                                        cadastro_economico.inscricao_economica
                                        ,CASE WHEN cadastro_economico_empresa_direito.numcgm IS NOT NULL THEN
                                                cadastro_economico_empresa_direito.numcgm
                                            WHEN cadastro_economico_autonomo.numcgm IS NOT NULL THEN
                                                cadastro_economico_autonomo.numcgm
                                        ELSE 
                                                cadastro_economico_empresa_fato.numcgm
                                        END as numcgm 
                                    FROM economico.cadastro_economico
                                    LEFT JOIN economico.cadastro_economico_empresa_fato
                                        ON cadastro_economico_empresa_fato.inscricao_economica = cadastro_economico.inscricao_economica    
                                    LEFT JOIN economico.cadastro_economico_autonomo
                                        ON cadastro_economico_autonomo.inscricao_economica = cadastro_economico.inscricao_economica
                                    LEFT JOIN economico.cadastro_economico_empresa_direito
                                        ON cadastro_economico_empresa_direito.inscricao_economica = cadastro_economico.inscricao_economica
                        ) as economico_empresa
                            ON economico_empresa.inscricao_economica = cadastro_economico.inscricao_economica
                        JOIN sw_cgm_pessoa_juridica
                            ON sw_cgm_pessoa_juridica.numcgm = economico_empresa.numcgm
                        JOIN sw_cgm
                            ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                ";
        return $stSql;
    }

}