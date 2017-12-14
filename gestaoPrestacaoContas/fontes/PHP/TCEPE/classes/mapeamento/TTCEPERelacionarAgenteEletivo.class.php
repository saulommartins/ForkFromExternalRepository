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
    * Pacote de configuração do TCEPE
    * Data de Criação   : 30/09/2014

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel Teixeira
    *
    $Id: TTCEPERelacionarAgenteEletivo.class.php 60109 2014-09-30 18:14:20Z michel $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEPERelacionarAgenteEletivo extends Persistente
{
    public function TTCEPERelacionarAgenteEletivo()
    {
        parent::Persistente();
        $this->setTabela("tcepe.agente_eletivo");
        $this->setCampoCod('exercicio');
        $this->setComplementoChave('cod_entidade,cod_remuneracao,cod_norma,cod_cargo');
        
        $this->AddCampo('exercicio'             , 'varchar'  , true , '4' , true  , true  );
        $this->AddCampo('cod_entidade'          , 'integer'  , true , ''  , true  , true  );
        $this->AddCampo('cod_tipo_remuneracao'  , 'integer'  , true , ''  , false , false );
        $this->AddCampo('cod_tipo_norma'        , 'integer'  , true , ''  , false , false );
        $this->AddCampo('cod_norma'             , 'integer'  , true , ''  , false , true  );
        $this->AddCampo('cod_cargo'             , 'integer'  , true , ''  , true  , false );
    }

    public function listarAgenteEletivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListarAgenteEletivo().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListarAgenteEletivo()
    {
        $stSql  =" SELECT exercicio, cod_entidade, cod_tipo_remuneracao, cod_tipo_norma, cod_norma
                     FROM tcepe.agente_eletivo                    
                    WHERE agente_eletivo.cod_entidade   = ".$this->getDado('cod_entidade')."
                      AND agente_eletivo.exercicio      = '".$this->getDado('exercicio')."'
                 GROUP BY exercicio, cod_entidade, cod_tipo_remuneracao, cod_tipo_norma, cod_norma";

        return $stSql;
    }
    
    public function listarCargoAgenteEletivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro();
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListarCargoAgenteEletivo().$stCondicao.$stOrdem;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListarCargoAgenteEletivo()
    {
        $stSql  =" SELECT cod_cargo
                     FROM tcepe.agente_eletivo                    
                    WHERE agente_eletivo.cod_entidade   = ".$this->getDado('cod_entidade')."
                      AND agente_eletivo.exercicio      = '".$this->getDado('exercicio')."'
                      AND agente_eletivo.cod_tipo_remuneracao = ".$this->getDado('cod_tipo_remuneracao')."
                      AND agente_eletivo.cod_tipo_norma = ".$this->getDado('cod_tipo_norma')."
                      AND agente_eletivo.cod_norma = ".$this->getDado('cod_norma');

        return $stSql;
    }
    
    
}
