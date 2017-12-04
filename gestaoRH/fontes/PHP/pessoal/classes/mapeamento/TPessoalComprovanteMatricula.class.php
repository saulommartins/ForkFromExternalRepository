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
  * Classe de mapeamento da tabela PESSOAL.COMPROVANTE_MATRICULA
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.COMPROVANTE_MATRICULA
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida
                           Vandre Miguel Ramos
  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalComprovanteMatricula extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalComprovanteMatricula()
{
    parent::Persistente();
    $this->setTabela('pessoal.comprovante_matricula');

    $this->setCampoCod('cod_comprovante');
    $this->setComplementoChave('');

    $this->AddCampo('cod_comprovante','integer',true,'',true,false);
    $this->AddCampo('dt_apresentacao','date',true,'',false,false);
    $this->AddCampo('apresentada','boolean',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT                                                        \n";
    $stSql.= "    to_char(dt_apresentacao,'dd/mm/yyyy') as dt_apresentacao,  \n";
    $stSql.= "    apresentada                                                \n";
    $stSql.= "FROM                                                           \n";
    $stSql.= "   pessoal.dependente_comprovante_matricula PDCM,          \n";
    $stSql.= "   pessoal.dependente                         PD,          \n";
    $stSql.= "   ".$this->getTabela ()."                       PCM           \n";
    $stSql.= "WHERE                                                          \n";
    $stSql.= "   PD.cod_dependente    = PDCM.cod_dependente    and           \n";
    $stSql.= "   PDCM.cod_comprovante = PCM.cod_comprovante                  \n";

    return $stSql;
}

}
