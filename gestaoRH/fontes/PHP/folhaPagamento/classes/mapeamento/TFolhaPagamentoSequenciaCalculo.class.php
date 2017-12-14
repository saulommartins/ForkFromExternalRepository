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
    * Classe de mapeamento da tabela FOLHAPAGAMENTO.SEQUENCIA_CALCULO
    * Data de Criação: 24/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Eduardo Antunez

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.SEQUENCIA_CALCULO
  * Data de Criação: 24/11/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Eduardo Antunez

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoSequenciaCalculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoSequenciaCalculo()
{
    parent::Persistente();
    $this->setTabela('folhapagamento.sequencia_calculo');

    $this->setCampoCod('cod_sequencia');
    $this->setComplementoChave('');

    $this->AddCampo('cod_sequencia', 'integer', true, ''       , true, false);
    $this->AddCampo('sequencia'    , 'integer', true, ''       ,false, false);
    $this->AddCampo('descricao'    , 'varchar', true, 'varchar',false, false);
    $this->AddCampo('complemento'  , 'varchar', true, 'varchar',false, false);

}

function validaExclusao($stFiltro = "", $boTransacao = "")
{
    $obErro = new erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaValidaExclusao().$stFiltro;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $obErro->setDescricao('A sequência não pode ser excluída, pois está sendo utilizada por um evento.');
        }
    }

    return $obErro;
}

function montaValidaExclusao()
{
    $stSQL .="SELECT cod_sequencia                                       \n";
    $stSQL .="  FROM folhapagamento.sequencia_calculo_evento             \n";
    $stSQL .=" WHERE cod_sequencia = ".$this->getDado('cod_sequencia')." \n";

    return $stSQL;
}

}
