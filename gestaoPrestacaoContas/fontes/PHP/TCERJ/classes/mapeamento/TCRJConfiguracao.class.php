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
    * Classe de mapeamento da tabela EMPENHO.PRE_EMPENHO
    * Data de Criação: 30/11/2004

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63835 $
    $Name$
    $Autor:$
    $Date: 2015-10-22 11:53:31 -0200 (Thu, 22 Oct 2015) $

    * Casos de uso: uc-06.02.01

*/

/*
$Log$
Revision 1.5  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:42:06  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");

/**
  * Efetua conexão com a tabela  EMPENHO.PRE_EMPENHO
  * Data de Criação: 30/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCRJConfiguracao extends TAdministracaoConfiguracao
{
/**
    * Método Construtor
    * @access Private
*/
function TCRJConfiguracao()
{
    parent::TAdministracaoConfiguracao();
    $this->SetDado("exercicio",Sessao::getExercicio());
    $this->SetDado("cod_modulo",32);
}

}
