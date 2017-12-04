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
  * Classe de mapeamento da tabela PESSOAL.ESPECIALIDADE_PADRAO
  * Data de Criação: 19/08/2005

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ESPECIALIDADE_PADRAO
  * Data de Criação: 19/08/2005

  * @author Analista: Vandre Miguel Ramos
  * @author Desenvolvedor: Andre Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalEspecialidadePadrao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPessoalEspecialidadePadrao()
    {
        parent::Persistente();
        $this->setTabela('pessoal.especialidade_padrao');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_especialidade, cod_padrao');

        $this->AddCampo('cod_especialidade', 'integer'  , true , '', true, true  );
        $this->AddCampo('cod_padrao'       , 'integer'  , true , '', true, true  );
        $this->AddCampo('timestamp'        , 'timestamp', false, '', true, false );
    }

function montaRecuperaRelacionamento()
{
    $stSql .= "    SELECT especialidade_padrao.*                                                                \n";
    $stSql .= "      FROM pessoal.especialidade_padrao                                 \n";
    $stSql .= "INNER JOIN (   SELECT cod_especialidade                                                          \n";
    $stSql .= "                    , max(timestamp) as timestamp                                                \n";
    $stSql .= "                 FROM pessoal.especialidade_padrao                      \n";
    $stSql .= "            GROUP BY cod_especialidade) as max_especialidade_padrao                              \n";
    $stSql .= "        ON max_especialidade_padrao.cod_especialidade = especialidade_padrao.cod_especialidade   \n";
    $stSql .= "       AND max_especialidade_padrao.timestamp = especialidade_padrao.timestamp                   \n";

    return $stSql;
}
}
