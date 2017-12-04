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
    * Classe de mapeamento da tabela PESSOAL.GRADE_HORARIO
    * Data de Criação: 13/09/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.GRADE_HORARIO
  * Data de Criação: 13/09/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalGradeHorario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalGradeHorario()
{
    parent::Persistente();
    $this->setTabela('pessoal.grade_horario');

    $this->setCampoCod('cod_grade');
    $this->setComplementoChave('');

    $this->AddCampo('cod_grade','integer',true,'',true,false);
    $this->AddCampo('descricao','char',true,'80',false,false);

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
            $obErro->setDescricao('Esta grade de horário está sendo utilizado por um servidor ou estagiário, por esse motivo não pode ser excluída!');
        }
    }

    return $obErro;
}

function montaValidaExclusao()
{
    $stSQL .= " SELECT cod_grade                                           \n";
    $stSQL .= "  FROM pessoal.contrato_servidor   \n";
    $stSQL .= " WHERE cod_grade = ".$this->getDado('cod_grade')."          \n";
    $stSQL .= " UNION                                                      \n";
    $stSQL .= " SELECT cod_grade                                           \n";
    $stSQL .= "   FROM estagio.estagiario_estagio \n";
    $stSQL .= " WHERE cod_grade = ".$this->getDado('cod_grade')."          \n";

    return $stSQL;
}

}
