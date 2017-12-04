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
  * Mapeamento de Tipo de Convênio
  * Data de criação : 09/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @package URBEM
  * @subpackage Mapeamento

  * $Id: TMONTipoConvenio.class.php 63839 2015-10-22 18:08:07Z franver $

  Caso de uso: uc-05.05.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

class TMONTipoConvenio extends Persistente
{

    /**
        * Método Construtor
        * @access Private
    */
    public function TMONTipoConvenio()
    {
        parent::Persistente();
        $this->setTabela('monetario.tipo_convenio');

        $this->setCampoCod('cod_tipo');
        $this->setComplementoChave('');

        $this->AddCampo('cod_tipo','integer',true,''  ,true ,false);
        $this->AddCampo('nom_tipo','varchar',true,'60',false,false);
        $this->AddCampo('cod_modulo','integer',true,'',false,true);
        $this->AddCampo('cod_biblioteca','integer',true,'',false,true);
        $this->AddCampo('cod_funcao','integer',true,'',false,true);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = '  SELECT  cod_tipo
                        ,   nom_tipo
                        ,   cod_modulo
                        ,   cod_biblioteca
                        ,   cod_funcao
                      FROM  '.$this->getTabela().'
                     WHERE ';

        if ($this->getDado('cod_tipo') != '') {
            $stSql .= "cod_tipo = ".$this->getDado('cod_tipo')." AND  ";
        }
        if ($this->getDado('cod_modulo') != '') {
            $stSql .= "cod_modulo = ".$this->getDado('cod_modulo')." AND  ";
        }
        if ($this->getDado('cod_biblioteca') != '') {
            $stSql .= "cod_biblioteca = ".$this->getDado('cod_biblioteca')." AND  ";
        }
        if ($this->getDado('cod_funcao') != '') {
            $stSql .= "cod_funcao = ".$this->getDado('cod_funcao')." AND  ";
        }

        return substr($stSql,0,-6);

    }
}
