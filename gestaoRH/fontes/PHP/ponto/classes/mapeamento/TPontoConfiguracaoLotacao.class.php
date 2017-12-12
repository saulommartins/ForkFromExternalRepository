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
    * Classe de mapeamento da tabela ponto.configuracao_lotacao
    * Data de Criação: 15/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoConfiguracaoLotacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPontoConfiguracaoLotacao()
    {
        parent::Persistente();
        $this->setTabela("ponto.configuracao_lotacao");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_configuracao,timestamp,cod_orgao');

        $this->AddCampo('cod_configuracao','integer'  ,true  ,'',true,'TPontoConfiguracaoParametrosGerais');
        $this->AddCampo('timestamp'       ,'timestamp',true  ,'',true,'TPontoConfiguracaoParametrosGerais');
        $this->AddCampo('cod_orgao'       ,'integer'  ,true  ,'',true,'TOrganogramaOrgao');

    }

    public function montarecuperaRelacionamento()
    {
        $stSql .= "    SELECT configuracao_lotacao.*                                                                \n";
        $stSql .= "      FROM ponto.configuracao_lotacao                                   \n";
        $stSql .= "INNER JOIN ponto.configuracao_relogio_ponto                             \n";
        $stSql .= "        ON configuracao_relogio_ponto.cod_configuracao = configuracao_lotacao.cod_configuracao   \n";
        $stSql .= "       AND configuracao_relogio_ponto.ultimo_timestamp = configuracao_lotacao.timestamp          \n";
        $stSql .= "     WHERE NOT EXISTS (SELECT 1                                                                  \n";
        $stSql .= "                         FROM ponto.configuracao_relogio_ponto_exclusao \n";
        $stSql .= "                        WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)\n";

        return $stSql;
    }

    public function recuperaLotacoesOcupadas(&$rsRecordset,$stFiltro="",$stOrdem="")
    {
        $obErro = $this->executaRecupera("montaRecuperaLotacoesOcupadas",$rsRecordset,$stFiltro,$stOrdem);

        return $obErro;
    }

    public function montaRecuperaLotacoesOcupadas()
    {
        $stSql .= "
        SELECT configuracao_lotacao.*                                                                    
             , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao  
             , vw_orgao_nivel.orgao                                                                      
             , vw_orgao_nivel.orgao AS cod_estrutural                                                    

          FROM ponto.configuracao_lotacao                                                                

    INNER JOIN ponto.configuracao_relogio_ponto                                                          
            ON configuracao_relogio_ponto.cod_configuracao = configuracao_lotacao.cod_configuracao       
           AND configuracao_relogio_ponto.ultimo_timestamp = configuracao_lotacao.timestamp              

    INNER JOIN organograma.orgao                                                                         
            ON orgao.cod_orgao = configuracao_lotacao.cod_orgao                                          

    INNER JOIN organograma.vw_orgao_nivel                                                                
            ON vw_orgao_nivel.cod_orgao = orgao.cod_orgao                                                

    INNER JOIN organograma.organograma
            ON organograma.cod_organograma = vw_orgao_nivel.cod_organograma
           AND organograma.ativo = true ";

        return $stSql;
    }

    public function recuperaLotacoesDisponiveis(&$rsRecordset,$stFiltro="",$stOrdem="")
    {
        $obErro = $this->executaRecupera("montaRecuperaLotacoesDisponiveis",$rsRecordset,$stFiltro,$stOrdem);

        return $obErro;
    }

    public function montaRecuperaLotacoesDisponiveis()
    {
        $stSql .= "
        SELECT recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao   
             , orgao.cod_orgao                                                                            
             , vw_orgao_nivel.orgao                                                                       
             , vw_orgao_nivel.orgao AS cod_estrutural                                                     
        
          FROM organograma.orgao                                                                          
        
    INNER JOIN organograma.vw_orgao_nivel                                                                 
            ON vw_orgao_nivel.cod_orgao = orgao.cod_orgao                                                 
        
    INNER JOIN organograma.organograma
            ON organograma.cod_organograma = vw_orgao_nivel.cod_organograma
           AND organograma.ativo = true 

         WHERE NOT EXISTS (SELECT 1                                                                       
                             FROM ponto.configuracao_lotacao                                              
                             JOIN ponto.configuracao_relogio_ponto                                        
                               ON configuracao_relogio_ponto.cod_configuracao = configuracao_lotacao.cod_configuracao
                              AND configuracao_relogio_ponto.ultimo_timestamp = configuracao_lotacao.timestamp       
                            WHERE configuracao_lotacao.cod_orgao = orgao.cod_orgao                                   
                              AND configuracao_lotacao.cod_configuracao = ".$this->getDado("cod_configuracao")."     
                              AND configuracao_lotacao.timestamp = '".$this->getDado("timestamp")."') ";

        return $stSql;
    }

    public function recuperaConfiguracaoContrato(&$rsRecordset,$stFiltro="",$stOrdem="")
    {
        $obErro = $this->executaRecupera("montaRecuperaConfiguracaoContrato",$rsRecordset,$stFiltro,$stOrdem);

        return $obErro;
    }

    public function montaRecuperaConfiguracaoContrato()
    {
        $stSql .= "     SELECT configuracao_relogio_ponto.*                                                                                           \n";
        $stSql .= "       FROM pessoal.contrato                                                                              \n";
        $stSql .= " INNER JOIN pessoal.contrato_servidor                                                                     \n";
        $stSql .= "         ON contrato.cod_contrato = contrato_servidor.cod_contrato                                                                 \n";
        $stSql .= " INNER JOIN pessoal.contrato_servidor_orgao                                                               \n";
        $stSql .= "         ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato                                                  \n";
        $stSql .= " INNER JOIN (  SELECT cod_contrato                                                                                                 \n";
        $stSql .= "                    , max(timestamp) as timestamp                                                                                  \n";
        $stSql .= "                 FROM pessoal.contrato_servidor_orgao                                                     \n";
        $stSql .= "             GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                 \n";
        $stSql .= "         ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato                                        \n";
        $stSql .= "        AND max_contrato_servidor_orgao.timestamp = contrato_servidor_orgao.timestamp                                              \n";
        $stSql .= " INNER JOIN ponto.configuracao_lotacao                                                                    \n";
        $stSql .= "         ON contrato_servidor_orgao.cod_orgao = configuracao_lotacao.cod_orgao                                                     \n";
        $stSql .= " INNER JOIN ponto.configuracao_relogio_ponto                                                              \n";
        $stSql .= "         ON configuracao_lotacao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao                                    \n";
        $stSql .= "        AND configuracao_lotacao.timestamp = configuracao_relogio_ponto.ultimo_timestamp                                           \n";
        $stSql .= "      WHERE NOT EXISTS (SELECT 1                                                                                                   \n";
        $stSql .= "                          FROM ponto.configuracao_relogio_ponto_exclusao                                  \n";
        $stSql .= "                         WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao) \n";

        return $stSql;
    }

}

?>
