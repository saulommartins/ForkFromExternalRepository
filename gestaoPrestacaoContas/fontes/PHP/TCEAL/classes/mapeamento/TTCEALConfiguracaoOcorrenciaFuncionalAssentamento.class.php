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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 17/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEALConfiguracaoOcorrenciaFuncionalAssentamento extends Persistente
{
    public function TTCEALConfiguracaoOcorrenciaFuncionalAssentamento()
    {
        parent::Persistente();
        $this->setTabela("tceal.ocorrencia_funcional_assentamento");
        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_entidade,cod_ocorrencia,cod_assentamento');
        
        $this->AddCampo('exercicio'        , 'varchar'  , true , '4' , true  , true  );
        $this->AddCampo('cod_entidade'     , 'integer'  , true , ''  , true  , true  );
        $this->AddCampo('cod_ocorrencia'   , 'integer'  , true , ''  , true  , true  );
        $this->AddCampo('cod_assentamento' , 'integer'  , true , ''  , true  , true  );
    }

    public function excluirAssentamentos()
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();

        $stSql = $this->montaExcluirAssentamentos();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaDML($stSql);
    }
    
    public function montaExcluirAssentamentos()
    {
        $stSql  ="   DELETE FROM tceal.ocorrencia_funcional_assentamento           \n";
        $stSql  .="        WHERE cod_entidade = ".Sessao::read('cod_entidade')."   \n";
        $stSql  .="        AND   exercicio    = '".Sessao::getExercicio()."'       \n";

        return $stSql;
    }

    public function buscarAssentamentos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaBuscarAssentamentos().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaBuscarAssentamentos()
    {
        $stSql  =" SELECT ocorrencia_funcional_assentamento.cod_ocorrencia                                        
                        , assentamento_assentamento.cod_assentamento
                        , assentamento_assentamento.descricao 
                     FROM tceal.ocorrencia_funcional_assentamento
              
               INNER JOIN tceal.ocorrencia_funcional
                       ON ocorrencia_funcional.cod_ocorrencia = ocorrencia_funcional_assentamento.cod_ocorrencia
                      AND ocorrencia_funcional.exercicio = ocorrencia_funcional_assentamento.exercicio
               
               INNER JOIN pessoal.assentamento_assentamento                                                               
                       ON assentamento_assentamento.cod_assentamento = ocorrencia_funcional_assentamento.cod_assentamento
                    
                    WHERE ocorrencia_funcional_assentamento.cod_entidade   = ".$this->getDado('cod_entidade')."
                      AND ocorrencia_funcional_assentamento.exercicio      = '".$this->getDado('exercicio')."'
                      AND ocorrencia_funcional_assentamento.cod_ocorrencia = ".$this->getDado('cod_ocorrencia');

        return $stSql;
    }
}
