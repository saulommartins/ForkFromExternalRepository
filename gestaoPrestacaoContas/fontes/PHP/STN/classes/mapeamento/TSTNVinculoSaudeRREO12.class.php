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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
    * Mapeamento da tabela stn.vinculo_saude_rreo12
    * Data de Criação   : 28/11/2016

    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Configuração

*/

include_once CLA_PERSISTENTE;

class TSTNVinculoSaudeRREO12 extends Persistente
{

    public function TSTNVinculoSaudeRREO12()
    {
        parent::Persistente();

        $this->setTabela   ('stn.vinculo_saude_rreo12');
        $this->setCampoCod ('cod_receita');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('cod_receita', 'integer', true, '' , true , false);
        $this->AddCampo('exercicio', 'varchar', true, '' , false, false);
    }
    
        
    function recuperaRelacionamento(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaRelacionamento();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaRelacionamento()
    {
        $stSql = " 	SELECT receita.cod_receita
                         , conta_receita.descricao
                         , vinculo_saude_rreo12.exercicio
                      FROM stn.vinculo_saude_rreo12
                INNER JOIN orcamento.receita
                        ON receita.cod_receita = vinculo_saude_rreo12.cod_receita
                       AND receita.exercicio = vinculo_saude_rreo12.exercicio
                INNER JOIN orcamento.conta_receita
                        ON conta_receita.cod_conta = receita.cod_conta
                       AND conta_receita.exercicio = receita.exercicio
                     WHERE vinculo_saude_rreo12.exercicio = '".Sessao::getExercicio()."' ";
    
        return $stSql;
    }
    
}
