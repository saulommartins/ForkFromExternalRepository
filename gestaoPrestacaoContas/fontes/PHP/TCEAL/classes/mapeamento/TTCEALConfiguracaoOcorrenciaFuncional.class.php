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

class TTCEALConfiguracaoOcorrenciaFuncional extends Persistente
{
    public function TTCEALConfiguracaoOcorrenciaFuncional()
    {
        parent::Persistente();
        $this->setTabela("tceal.ocorrencia_funcional");
        $this->setCampoCod('cod_ocorrencia');

        $this->AddCampo('cod_ocorrencia' , 'integer'  , true , ''    , true  , true  );
        $this->AddCampo('descricao'      , 'varchar'  , true , '100' , false , true  );
        $this->AddCampo('exercicio'      , 'varchar'  , true , '4'   , false , true  );
    }

    public function buscarOcorrencias(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaBuscarOcorrencias().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaBuscarOcorrencias()
    {
        $stSql  ="       SELECT ocorrencia_funcional.cod_ocorrencia
                              , ocorrencia_funcional.descricao
                           FROM tceal.ocorrencia_funcional_assentamento
                     
                     INNER JOIN tceal.ocorrencia_funcional
                             ON ocorrencia_funcional.cod_ocorrencia = ocorrencia_funcional_assentamento.cod_ocorrencia
                       
                          WHERE ocorrencia_funcional_assentamento.cod_entidade = ".$this->getDado('cod_entidade')."
                            AND ocorrencia_funcional_assentamento.exercicio    = '".$this->getDado('exercicio')."'
                       
                       GROUP BY ocorrencia_funcional.cod_ocorrencia
                              , ocorrencia_funcional.descricao
                       
                       ORDER BY ocorrencia_funcional.cod_ocorrencia ";

        return $stSql;
    }
}
