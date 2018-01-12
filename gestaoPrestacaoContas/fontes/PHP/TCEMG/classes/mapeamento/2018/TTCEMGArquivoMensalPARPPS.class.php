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
    * Classe de mapeamento
    * Data de Criação:  27/01/2014

    * @author Analista: Sergio
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGArquivoMensalPARPPS.class.php 62269 2015-04-15 18:28:39Z franver $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGArquivoMensalPARPPS extends Persistente
{
    public function TTCEMGArquivoMensalPARPPS()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDados()
    {
        $stSql = "
                  Select tipo_registro
                        , cod_orgao
                        , vl_saldo_financeiro_exercicioanterior

                    From ( Select 10 as tipo_registro
                                , orgao_sicom.valor as cod_orgao
                                , (Select vl_saldo_financeiro from stn.rreo_anexo_13 where ano = (CAST('".Sessao::getExercicio()."' AS INT)-1)::TEXT) as vl_saldo_financeiro_exercicioanterior
                            From stn.rreo_anexo_13
                            JOIN (SELECT valor::integer
                                        , configuracao_entidade.exercicio
                                        , configuracao_entidade.cod_entidade
                                     FROM tcemg.orgao
                               INNER JOIN administracao.configuracao_entidade
                                       ON configuracao_entidade.valor::integer = orgao.num_orgao
                                    WHERE configuracao_entidade.cod_entidade IN (1,2,3)  AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                                  )  AS orgao_sicom
                                       ON orgao_sicom.exercicio='2014'
                                      AND orgao_sicom.cod_entidade = rreo_anexo_13.cod_entidade
                            Where rreo_anexo_13.ano = '".Sessao::getExercicio()."'
                           ) as tabela";

        return $stSql;
    }

     function recuperaDadosRPPS(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
     {
        return $this->executaRecupera("montaRecuperaDadosRPPS",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosRPPS()
    {
        $stSql = "
                  Select tipo_registro
                , cod_orgao
                , exercicio
                , vl_receita_previdenciaria
                , vl_despesa_previdenciaria

                    From ( Select 20 as tipo_registro
                                , orgao_sicom.valor as cod_orgao
                                , rreo_anexo_13.ano::VARCHAR as exercicio
                                , rreo_anexo_13.vl_receita_previdenciaria as vl_receita_previdenciaria
                                , rreo_anexo_13.vl_despesa_previdenciaria as vl_despesa_previdenciaria
                            From stn.rreo_anexo_13
                            JOIN (SELECT valor::integer
                                        , configuracao_entidade.exercicio
                                        , configuracao_entidade.cod_entidade
                                     FROM tcemg.orgao
                               INNER JOIN administracao.configuracao_entidade
                                       ON configuracao_entidade.valor::integer = orgao.num_orgao
                                    WHERE configuracao_entidade.cod_entidade IN (1,2,3)  AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                                  )  AS orgao_sicom
                                       ON orgao_sicom.exercicio='2014'
                                      AND orgao_sicom.cod_entidade = rreo_anexo_13.cod_entidade
                            Where 
                                rreo_anexo_13.ano BETWEEN '".(Sessao::getExercicio()-1)."' AND (select max(rreo_anexo_13.ano) from stn.rreo_anexo_13)

                            Group by  rreo_anexo_13.ano
                                    , orgao_sicom.valor
                                    , rreo_anexo_13.vl_receita_previdenciaria
                                    , rreo_anexo_13.vl_despesa_previdenciaria
                           ) as tabela";

        return $stSql;
    }
    
    public function __destruct(){}

}

?>