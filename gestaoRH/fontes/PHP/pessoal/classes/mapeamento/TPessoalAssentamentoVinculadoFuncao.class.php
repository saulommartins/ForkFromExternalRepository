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
    * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_VINCULADO_FUNCAO
    * Data de Criação: 04/08/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_VINCULADO_FUNCAO
  * Data de Criação: 04/08/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoVinculadoFuncao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoVinculadoFuncao()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_vinculado_funcao');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento_assentamento,cod_condicao,timestamp,cod_assentamento');

    $this->AddCampo('timestamp','timestamp',false,'',true,true                 );
    $this->AddCampo('cod_assentamento_assentamento','integer',true,'',true,true);
    $this->AddCampo('cod_condicao','integer',true,'',true,true                 );
    $this->AddCampo('cod_assentamento','integer',true,'',true,true             );
    $this->AddCampo('dias_incidencia','integer,',false,'',true,false           );
    $this->AddCampo('dias_protelar_averbar','integer,',false,'',false,false    );
    $this->AddCampo('condicao','char',false,'1',true,false                     );
    $this->AddCampo('cod_funcao','integer',true,'',false,true                  );
    $this->AddCampo('cod_modulo','integer',true,'',false,true                  );
    $this->AddCampo('cod_biblioteca','integer',true,'',false,true              );

}

}
