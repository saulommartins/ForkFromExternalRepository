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
    * Classe de mapeamento da tabela ima.evento_composicao_Remuneracao
    * Data de Criação: 25/10/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TIMAEventoComposicaoRemuneracao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.04.12
*/
/*
$Log: base.php,v $
Revision 1.3  2007/07/25 13:47:01  souzadl
alterado

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.evento_composicao_Remuneracao
  * Data de Criação: 25/10/2007

  * @author Analista: Dagiane Vieira
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAEventoComposicaoRemuneracao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAEventoComposicaoRemuneracao()
{
    parent::Persistente();
    $this->setTabela("ima.evento_composicao_remuneracao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_evento');

    $this->AddCampo('exercicio' ,'varchar',true  ,'4'  ,true,'TIMAConfiguracaoRais');
    $this->AddCampo('cod_evento','integer',true  ,''   ,true,'TFolhaPagamentoEvento');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT evento.* \n";
    $stSql .= "  FROM folhapagamento.evento\n";
    $stSql .= "     , ima.evento_composicao_remuneracao\n";
    $stSql .= " WHERE evento.cod_evento = evento_composicao_Remuneracao.cod_evento\n";

    return $stSql;
}

}
?>
