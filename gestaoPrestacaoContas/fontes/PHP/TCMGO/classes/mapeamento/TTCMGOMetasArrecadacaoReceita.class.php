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
    * Classe de mapeamento da tabela TCMGO.METAS_ARRECADAÇÃO_RECEITAS
    * Data de Criação: 20/02/2014

    * @author Analista: Ane Caroline
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento
    *
    * $Id: $
    *
    * $Name: $
    * $Date: $
    * $Author: $
    * $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCMGOMetasArrecadacaoReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOMetasArrecadacaoReceita()
    {
        parent::Persistente();
        $this->setTabela('tcmgo.metas_arrecadacao_receita');

        $this->setCampoCod('exercicio');
        $this->setComplementoChave('');

        $this->AddCampo('exercicio'                       , 'varchar', true, '4'   ,true ,false);
        $this->AddCampo('meta_arrecadacao_1_bi'           , 'numeric', false,'14,2',false,false);
        $this->AddCampo('meta_arrecadacao_2_bi'           , 'numeric', false,'14,2',false,false);
        $this->AddCampo('meta_arrecadacao_3_bi'           , 'numeric', false,'14,2',false,false);
        $this->AddCampo('meta_arrecadacao_4_bi'           , 'numeric', false,'14,2',false,false);
        $this->AddCampo('meta_arrecadacao_5_bi'           , 'numeric', false,'14,2',false,false);
        $this->AddCampo('meta_arrecadacao_6_bi'           , 'numeric', false,'14,2',false,false);
    }
    
    public function recuperaMetasArrecadacaoReceita(&$rsRecordSet)
    {
        return $this->executaRecupera("montaRecuperaMetasArrecadacaoReceita",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMetasArrecadacaoReceita()
    {
        $stSql = "
        SELECT exercicio
             , meta_arrecadacao_1_bi AS meta_arrecadacao_1_bi
             , meta_arrecadacao_2_bi AS meta_arrecadacao_2_bi
             , meta_arrecadacao_3_bi AS meta_arrecadacao_3_bi
             , meta_arrecadacao_4_bi AS meta_arrecadacao_4_bi
             , meta_arrecadacao_5_bi AS meta_arrecadacao_5_bi
             , meta_arrecadacao_6_bi AS meta_arrecadacao_6_bi
            
          FROM tcmgo.metas_arrecadacao_receita
         WHERE exercicio = ('".$this->getDado('exercicio')."')
         ORDER BY exercicio
        ";

        return $stSql;
    }
    
    public function __destruct(){}

}
?>
