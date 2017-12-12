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
    * Classe de mapeamento da view SAMLINK_VW_SIAM_AUTENT
    * Data de Criação: 11/02/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson Wolowski Gonçalves

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.04
*/

/*
$Log$
Revision 1.6  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class VSamlinkSiamAutent extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VSamlinkSiamAutent()
{
    parent::Persistente();
    $this->setTabela("samlink.vw_siam_autent");

    $this->setCampoCod('');
    //$this->setComplementoChave('data,numero');

    $this->AddCampo( 'conta'   ,'integer'    );
    $this->AddCampo( 'valor'   ,'numeric'    );
    $this->AddCampo( 'empen'   ,'character'  );
    $this->AddCampo( 'numpre'  ,'character'  );
    $this->AddCampo( 'data'    ,'date'       );
    $this->AddCampo( 'hora'    ,'character'  );
    $this->AddCampo( 'nome'    ,'character'  );
    $this->AddCampo( 'cheque'  ,'integer'    );
    $this->AddCampo( 'compen'  ,'boolean'    );
    $this->AddCampo( 'term'    ,'character'  );
    $this->AddCampo( 'rec01'   ,'character'  );
    $this->AddCampo( 'rec02'   ,'character'  );
    $this->AddCampo( 'rec03'   ,'character'  );
    $this->AddCampo( 'rec04'   ,'character'  );
    $this->AddCampo( 'rec05'   ,'character'  );
    $this->AddCampo( 'rec06'   ,'character'  );
    $this->AddCampo( 'rec07'   ,'character'  );
    $this->AddCampo( 'rec08'   ,'character'  );
    $this->AddCampo( 'rec09'   ,'character'  );
    $this->AddCampo( 'rec10'   ,'character'  );
    $this->AddCampo( 'rec11'   ,'character'  );
    $this->AddCampo( 'rec12'   ,'character'  );
    $this->AddCampo( 'rec13'   ,'character'  );
    $this->AddCampo( 'rec14'   ,'character'  );
    $this->AddCampo( 'rec15'   ,'character'  );
    $this->AddCampo( 'rec16'   ,'character'  );
    $this->AddCampo( 'rec17'   ,'character'  );
    $this->AddCampo( 'rec18'   ,'character'  );
    $this->AddCampo( 'rec19'   ,'character'  );
    $this->AddCampo( 'rec20'   ,'character'  );
    $this->AddCampo( 'vlr01'   ,'numeric'    );
    $this->AddCampo( 'vlr02'   ,'numeric'    );
    $this->AddCampo( 'vlr03'   ,'numeric'    );
    $this->AddCampo( 'vlr04'   ,'numeric'    );
    $this->AddCampo( 'vlr05'   ,'numeric'    );
    $this->AddCampo( 'vlr06'   ,'numeric'    );
    $this->AddCampo( 'vlr07'   ,'numeric'    );
    $this->AddCampo( 'vlr08'   ,'numeric'    );
    $this->AddCampo( 'vlr09'   ,'numeric'    );
    $this->AddCampo( 'vlr10'   ,'numeric'    );
    $this->AddCampo( 'vlr11'   ,'numeric'    );
    $this->AddCampo( 'vlr12'   ,'numeric'    );
    $this->AddCampo( 'vlr13'   ,'numeric'    );
    $this->AddCampo( 'vlr14'   ,'numeric'    );
    $this->AddCampo( 'vlr15'   ,'numeric'    );
    $this->AddCampo( 'vlr16'   ,'numeric'    );
    $this->AddCampo( 'vlr17'   ,'numeric'    );
    $this->AddCampo( 'vlr18'   ,'numeric'    );
    $this->AddCampo( 'vlr19'   ,'numeric'    );
    $this->AddCampo( 'vlr20'   ,'numeric'    );
    $this->AddCampo( 'estorn'  ,'boolean'    );
    $this->AddCampo( 'autent'  ,'integer'    );
    $this->AddCampo( 'entidade','integer'    );
}
}
