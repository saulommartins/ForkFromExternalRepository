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
     * Classe de mapeamento para a tabela IMOBILIARIO.BAIXA_UNIDADE_AUTONOMA
     * Data de Criação: 07/09/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: TCIMBaixaUnidadeAutonoma.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.8  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.BAIXA_UNIDADE_AUTONOMA
  * Data de Criação: 07/09/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Cassiano de Vasconcellos Ferrerira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCIMBaixaUnidadeAutonoma extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCIMBaixaUnidadeAutonoma()
{
    parent::Persistente();
    $this->setTabela('imobiliario.baixa_unidade_autonoma');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_municipal,cod_tipo,cod_construcao,timestamp');

    $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
    $this->AddCampo('cod_tipo','integer',true,'',true,true);
    $this->AddCampo('cod_construcao','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('justificativa','text',true,'',false,false);
    $this->AddCampo('dt_inicio','date',false,'',false,false);
    $this->AddCampo('dt_termino','date',false,'',false,false);
    $this->AddCampo('justificativa_termino','text',false,'',false,false);
}
}
