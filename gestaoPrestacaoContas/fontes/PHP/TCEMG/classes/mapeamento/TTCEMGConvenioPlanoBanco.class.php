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

    * Classe de mapeamento da tabela tcemg.convenio_plano_banco
    * Data de Criação   : 25/02/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: TTCEMGConvenioPlanoBanco.class.php 59719 2014-09-08 15:00:53Z franver $
    *
    * $Revision: 59719 $
    * $Author: franver $
    * $Date: 2014-09-08 12:00:53 -0300 (Mon, 08 Sep 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGConvenioPlanoBanco extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TTCEMGConvenioPlanoBanco()
    {
        parent::Persistente();
        $this->setTabela("tcemg.convenio_plano_banco");

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('exercicio,cod_plano');

        $this->AddCampo( 'cod_plano','integer' ,true, '' ,true,true );
        $this->AddCampo( 'cod_entidade' ,'integer' ,true, ''   ,false ,false  );
        $this->AddCampo( 'exercicio','varchar' ,true, '4' ,true,true );
        $this->AddCampo( 'num_convenio' ,'integer' ,true, ''   ,false ,false  );
        $this->AddCampo( 'dt_assinatura' ,'date' ,true, ''   ,false ,false  );

    }

    public function recuperaPlanoContaAnalitica(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
            $obErro      = new Erro;
            $obConexao   = new Conexao;
            $rsRecordSet = new RecordSet;

            if(trim($stOrdem))
                $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            $stSql = $this->montaRecuperaPlanoContaAnalitica().$stCondicao.$stOrdem;
            $this->setDebug( $stSql );
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

            return $obErro;
     }

/**
    * Seta dados para fazer o recuperaRelacionamentoPlanoContaAnalitica
    * @access Public
    * @return String $stSql
*/
    public function montaRecuperaPlanoContaAnalitica()
    {
        $stSQL  = "
            SELECT plano_conta.cod_estrutural
                      , plano_conta.exercicio
                      , plano_conta.nom_conta
                      , plano_conta.cod_conta
                      , publico.fn_mascarareduzida(plano_conta.cod_estrutural) as cod_reduzido
                      , plano_analitica.cod_plano
                      , plano_analitica.natureza_saldo
                      , convenio_plano_banco.num_convenio
                      , TO_CHAR(convenio_plano_banco.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
              FROM  contabilidade.plano_conta

      INNER JOIN contabilidade.plano_analitica
                  ON plano_conta.cod_conta = plano_analitica.cod_conta
                AND plano_conta.exercicio = plano_analitica.exercicio

    INNER JOIN contabilidade.plano_banco
                  ON plano_banco.cod_plano = plano_analitica.cod_plano
                AND plano_banco.exercicio = plano_analitica.exercicio

        LEFT JOIN tcemg.convenio_plano_banco
                  ON  convenio_plano_banco.cod_plano = plano_banco.cod_plano
                AND convenio_plano_banco.exercicio = plano_banco.exercicio

        WHERE plano_banco.exercicio = '".$this->getDado('exercicio')."'
             AND plano_banco.cod_entidade = ".$this->getDado('cod_entidade')."

             ORDER BY plano_conta.cod_estrutural
        ";

        return $stSQL;
    }
    
    public function __destruct(){}


}
