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
  * Classe de mapeamento da tabela ECONOMICO.SERVICO_ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMServicoAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.07
*/

/*
$Log$
Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.SERVICO_ATIVIDADE
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMServicoAtividade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMServicoAtividade()
{
    parent::Persistente();
    $this->setTabela('economico.servico_atividade');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_servico,cod_atividade');

    $this->AddCampo('cod_servico','integer',true,'',true,true);
    $this->AddCampo('cod_atividade','integer',true,'',true,true);
    $this->AddCampo('ativo', 'boolean', false, '', false, false);
}

function recuperaServicoAtividade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaServicoAtividade().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaServicoAtividade()
{
    $stSql .=" SELECT                                        \n";
    $stSql .="     SV.nom_servico,                           \n";
    $stSql .="     SV.cod_servico,                           \n";
    $stSql .="     SV.cod_estrutural,                           \n";
    $stSql .="     A.cod_atividade,                          \n";
    $stSql .="     A.nom_atividade                           \n";
    $stSql .=" FROM                                          \n";
    $stSql .="     economico.servico as SV,                    \n";
    $stSql .="     economico.servico_atividade as SA,          \n";
    $stSql .="     economico.atividade as A                    \n";
    $stSql .=" WHERE                                         \n";
    $stSql .="     A.cod_atividade = SA.cod_atividade AND    \n";
    $stSql .="     SA.cod_servico  = SV.cod_servico          \n";
   
    return $stSql;
}

}
