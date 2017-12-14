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

/*
 * Classe de mapeamento da tabela tcepe.responsavel_tecnico
 * Data de Criação: 17/10/2014
 * @author Desenvolvedor Evandro Melos
 * @package URBEM
 * @subpackage
 * $Id: TTCEPEResponsavelTecnico.class.php 60477 2014-10-23 17:51:04Z jean $
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEPEResponsavelTecnico extends Persistente
{
    /**
     * Método Construtor da classe de mapeamento
     *
     * @return void
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela  ('tcepe.responsavel_tecnico');

        $this->setCampoCod('cgm_responsavel');

        $this->AddCampo('cgm_responsavel' , 'integer', true , ''  , true  , true  );
        $this->AddCampo('cod_entidade'    , 'integer', true , ''  , false , true  );
        $this->AddCampo('exercicio'       , 'varchar', true , '4' , false , true  );
        $this->AddCampo('cod_tipo'        , 'integer', true , ''  , false , true  );
        $this->AddCampo('crc'             , 'varchar', false, '10', false , false );
        $this->AddCampo('dt_inicio'       , 'date'   , true , ''  , false , false );
        $this->AddCampo('dt_fim'          , 'date'   , true , ''  , false , false );
        
    }

    public function recuperaResponsavelTecnico(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaResponsavelTecnico().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaResponsavelTecnico()
    {
        $stSql = "  SELECT       
                            sw_entidade.nom_cgm AS nom_entidade
                            ,responsavel_tecnico.cgm_responsavel
                            ,sw_cgm.nom_cgm
                            ,tipo_responsavel.cod_tipo
                            ,tipo_responsavel.descricao
                            ,responsavel_tecnico.crc
                            ,entidade.cod_entidade
                            ,TO_CHAR(responsavel_tecnico.dt_inicio,'dd/mm/yyyy') as dt_inicio
                            ,TO_CHAR(responsavel_tecnico.dt_fim,'dd/mm/yyyy') as dt_fim
                    FROM tcepe.responsavel_tecnico 
                    JOIN sw_cgm
                         ON responsavel_tecnico.cgm_responsavel = sw_cgm.numcgm
                    JOIN orcamento.entidade
                         ON entidade.cod_entidade   = responsavel_tecnico.cod_entidade
                        AND entidade.exercicio      = responsavel_tecnico.exercicio
                    JOIN tcepe.tipo_responsavel
                         ON tipo_responsavel.cod_tipo    = responsavel_tecnico.cod_tipo
                    JOIN sw_cgm as sw_entidade
                         ON entidade.numcgm = sw_entidade.numcgm
        ";

        return $stSql;
    }

    public function recuperaArquivoTecnicoResponsavel(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaArquivoTecnicoResponsavel().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaArquivoTecnicoResponsavel()
    {
        $stSql = "  SELECT                      
                             sw_cgm_pessoa_fisica.cpf
                            , sw_cgm.nom_cgm as nom_tecnico
                            , (SELECT sw_cgm_pessoa_juridica.nom_fantasia
                                FROM sw_cgm_pessoa_juridica
                                WHERE numcgm = (SELECT CASE WHEN valor = '' THEN '0' ELSE valor END as valor
                                                FROM administracao.configuracao 
                                                WHERE exercicio = '".$this->getDado('exercicio')."' 
                                                AND cod_modulo = 63 
                                                AND parametro = 'provedor_sistema')::integer
                            ) as razao_social
                            , (SELECT sw_cgm.nom_cgm
                                FROM sw_cgm
                                WHERE numcgm = (SELECT CASE WHEN valor = '' THEN '0' ELSE valor END as valor
                                                FROM administracao.configuracao 
                                                WHERE exercicio = '".$this->getDado('exercicio')."' 
                                                AND cod_modulo = 63 
                                                AND parametro = 'provedor_sistema')::integer
                            ) as provedor_sistema
                            , sw_cgm.e_mail
                            , sw_cgm.logradouro
                            , sw_cgm.numero
                            , sw_cgm.complemento
                            , sw_cgm.bairro
                            , sw_municipio.nom_municipio as municipio
                            , sw_uf.sigla_uf as estado
                            , sw_cgm.cep
                            , SUBSTR(sw_cgm.fone_residencial,1,2) as ddd_telefone
                            , SUBSTR(sw_cgm.fone_residencial,3) as telefone_fixo
                            , sw_cgm.fone_celular as celular
                            , (SELECT sw_cgm_pessoa_juridica.cnpj
                                FROM sw_cgm_pessoa_juridica
                                WHERE numcgm = (SELECT CASE WHEN valor = '' THEN '0' ELSE valor END as valor
                                                FROM administracao.configuracao 
                                                WHERE exercicio = '".$this->getDado('exercicio')."' 
                                                AND cod_modulo = 63 
                                                AND parametro = 'provedor_sistema')::integer
                            ) as cnpj
                            , responsavel_tecnico.crc    
                            , tipo_responsavel.cod_tipo as tipo_tecnico
                    FROM sw_cgm 
                    JOIN tcepe.responsavel_tecnico  
                         ON responsavel_tecnico.cgm_responsavel = sw_cgm.numcgm 
                    JOIN tcepe.tipo_responsavel
                         ON tipo_responsavel.cod_tipo = responsavel_tecnico.cod_tipo  
                    LEFT JOIN sw_cgm_pessoa_fisica
                         ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                    JOIN sw_municipio
                         ON sw_municipio.cod_municipio  = sw_cgm.cod_municipio
                        AND sw_municipio.cod_uf         = sw_cgm.cod_uf
                    JOIN sw_uf
                         ON sw_uf.cod_uf = sw_municipio.cod_uf
                         
                   WHERE (responsavel_tecnico.dt_inicio BETWEEN TO_DATE('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('stDataFinal')."','dd/mm/yyyy')
                      OR responsavel_tecnico.dt_fim BETWEEN TO_DATE('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('stDataFinal')."','dd/mm/yyyy'))
                     AND responsavel_tecnico.cod_entidade = ".$this->getDado('stEntidade')."

        ";
        
        return $stSql;
    }

}

