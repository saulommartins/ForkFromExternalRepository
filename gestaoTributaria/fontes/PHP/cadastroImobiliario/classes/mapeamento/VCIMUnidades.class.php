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
     * Classe de mapeamento para a tabela IMOBILIARIO.UNIDADES (AUTONOMAS E DEPENDENTES)
     * Data de Criação: 24/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Mapeamento
    * $Id: VCIMUnidades.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
*/

/*
$Log$
Revision 1.7  2006/09/18 09:12:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  IMOBILIARIO.UNIDADES (AUTONOMAS E DEPENDENTES)
  * Data de Criação: 24/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class VCIMUnidades extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VCIMUnidades()
{
    parent::Persistente();
    $this->setTabela('imobiliario.vw_unidades');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_municipal,cod_tipo');

    $this->AddCampo('inscricao_municipal'       ,'integer'   );
    $this->AddCampo('cod_tipo'                  ,'integer'   );
    $this->AddCampo('cod_tipo_dependente'       ,'integer'   );
    $this->AddCampo('cod_construcao'            ,'integer'   );
    $this->AddCampo('timestamp'                 ,'timestamp' );
//    $this->AddCampo('numero'                    ,'varchar'   );
//    $this->AddCampo('complemento'               ,'varchar'   );
    $this->AddCampo('cod_construcao_dependente' ,'character' );
    $this->AddCampo('area'                      ,'numeric'   );
    $this->AddCampo('nom_tipo'                  ,'varchar'   );
    $this->AddCampo('tipo_unidade'              ,'varchar'   );
    $this->AddCampo('data_construcao'           ,'varchar'   );
}

}
