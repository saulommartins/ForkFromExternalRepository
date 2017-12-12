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
  * Classe de mapeamento da tabela ECONOMICO.NIVEL_SERVICO_VALOR
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMNivelServicoValor.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.02
*/

/*
$Log$
Revision 1.6  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.NIVEL_SERVICO_VALOR
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMNivelServicoValor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMNivelServicoValor()
{
    parent::Persistente();
    $this->setTabela('economico.nivel_servico_valor');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_nivel,cod_vigencia,cod_servico');

    $this->AddCampo('cod_nivel','integer',true,'',true,true);
    $this->AddCampo('cod_vigencia','integer',true,'',true,true);
    $this->AddCampo('cod_servico','integer',true,'',true,true);
    $this->AddCampo('valor','varchar',true,'20',false,false);

}

function AtualizaNivelServicoValor($boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaAtualizaNivelServicoValor();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

function montaAtualizaNivelServicoValor()
{
    $stSql  = "  UPDATE  economico.nivel_servico_valor \n";
    $stSql .= "  SET     valor = ". $this->getDado( "valor" )." \n";
    $stSql .= "  WHERE   valor = ". $this->getDado( "valorAntigo" )."::varchar\n";
    $stSql .= "  AND cod_nivel = ". $this->getDado( "nivel" )."\n";

    return $stSql;
}
}
