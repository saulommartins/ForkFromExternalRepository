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
    * Classe que faz o gerenciamento de documnetos para o sistema de Protocolo
    * Data de Criação   : 10/03/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 28299 $
    $Name$
    $Autor: $
    $Date: 2008-02-29 14:31:59 -0300 (Sex, 29 Fev 2008) $

    * Casos de uso: uc-03.01.02
                    uc-03.01.03
                    uc-03.01.04
                    uc-03.01.05
*/

/*
$Log$
Revision 1.8  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:27  diego

*/

class configPatrimonio
{
/**************************************************************************/
/**** Declaração das variáveis                                          ***/
/**************************************************************************/
    public $codNautreza;
    public $nomNatureza;
    public $codGrupo;
    public $nomGrupo;
    public $codPlano;
    public $anoE;
    public $depreciacao;
    public $codEspecie;
    public $nomEspecie;
    public $codAtributo;
    public $nomAtributo;
    public $tipo;
    public $valorPadrao;
/**************************************************************************/
/**** Método Construtor                                                 ***/
/**************************************************************************/
    public function configPatrimonio()
    {
        $this->codNautreza = "";
        $this->nomNatureza = "";
        $this->codGrupo = "";
        $this->nomGrupo = "";
        $this->codPlano = "";
        $this->AnoE = "";
        $this->depreciacao = "";
        $this->codEspecie = "";
        $this->nomEspecie = "";
        $this->codAtributo = "";
        $this->nomAtributo = "";
        $this->tipo = "";
        $this->valorPadrao = "";
        }

/**************************************************************************/
/**** Método que pega as variaveis para tratamento                      ***/
/**************************************************************************/
    public function setaVariaveisNatureza($codNatureza, $nomNatureza="")
    {
        $this->codNatureza = $codNatureza;
        $this->nomNatureza = $nomNatureza;
}//Fim método

/**************************************************************************/
/**** Método que faz a inserção das naturezas                           ***/
/**************************************************************************/
    public function insereNatureza()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO patrimonio.natureza (cod_natureza, nom_natureza) VALUES ('".$this->codNatureza."', '".$this->nomNatureza."')";
    if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que faz a alteração das naturezas                          ***/
/**************************************************************************/
    public function updateNatureza()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = "UPDATE patrimonio.natureza SET nom_natureza = '".$this->nomNatureza."' WHERE cod_natureza = ".$this->codNatureza;
    if ($dbConfig->executaSql($update))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que faz a exclusão das naturezas                           ***/
/**************************************************************************/
    public function deleteNatureza()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = "DELETE FROM patrimonio.natureza WHERE cod_natureza = ".$this->codNatureza;
    if ($dbConfig->executaSql($update))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que pega as variaveis para tratamento                      ***/
/**************************************************************************/
    public function setaVariaveisGrupo($codGrupo, $codNatureza, $nomGrupo="", $plano="", $anoE="", $depreciacao="")
    {
        $this->codNatureza = $codNatureza;
        $this->codGrupo = $codGrupo;
        $this->nomGrupo = $nomGrupo;
        $this->codPlano = $_REQUEST['codPlanoDebito'];
        $this->anoE     = $_REQUEST['anoE'];
//      $this->anoE     = $anoE;
        $this->depreciacao =  str_replace(",", ".", $_REQUEST['depreciacao'] );

}//Fim método

/**************************************************************************/
/**** Método que pega as variaveis para tratamento                      ***/
/**************************************************************************/
    public function setaVariaveisDeletaGrupo($codGrupo, $codNatureza, $codPlano, $anoE)
    {
        $this->codNatureza = $codNatureza;
        $this->codGrupo = $codGrupo;
        $this->codPlano = $codPlano;
        $this->anoE     = $anoE;

}//Fim método

/**************************************************************************/
/**** Método que faz a inserção dos Grupos                              ***/
/**************************************************************************/
    public function insereGrupo()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();

        $insert = "
                    INSERT INTO patrimonio.grupo
                        (cod_grupo, cod_natureza, nom_grupo, depreciacao)
                    VALUES
                        (".$this->codGrupo.",".$this->codNatureza.",'".$this->nomGrupo."','".$this->depreciacao."');";

        $insert.= "
                    INSERT INTO patrimonio.grupo_plano_analitica
                        (cod_grupo, cod_natureza, exercicio, cod_plano)
                    VALUES
                        (".$this->codGrupo.",".$this->codNatureza.",'".$this->anoE."','".$this->codPlano."')";

        if ($dbConfig->executaSql($insert)) {
         return true;
        } else {
         return false;
        }

/*
        if ($dbConfig->executaSql($insert)) {
             if ($dbConfig->executaSql($insert2)) {
                return true;
             } else {
                return false;
             }
        } else {
            return false;
        }
*/
        $dbConfig->fechaBd();
        }
/**************************************************************************/

/**** Método que faz a alteração das naturezas                          ***/
/**************************************************************************/
    public function updateGrupo($boAnalitica)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
     if ($boAnalitica) {
        $update = "
                    UPDATE
                        patrimonio.grupo
                    SET
                        nom_grupo = '".$this->nomGrupo."',
                        depreciacao = ".$this->depreciacao."
                    WHERE
                        cod_natureza = ".$this->codNatureza." AND
                        cod_grupo = ".$this->codGrupo.";";

       $update.= "
                    UPDATE
                        patrimonio.grupo_plano_analitica
                    SET
                        cod_plano    = ".$this->codPlano.",
                        exercicio    = '".$this->anoE."'
                    WHERE
                        cod_natureza = ".$this->codNatureza." AND
                        cod_grupo = ".$this->codGrupo." AND
                        exercicio = ".$this->anoE ;
     } else {
        $update = "
                    UPDATE
                        patrimonio.grupo
                    SET
                        nom_grupo = '".$this->nomGrupo."',
                        depreciacao = ".$this->depreciacao."
                    WHERE
                        cod_natureza = ".$this->codNatureza." AND
                        cod_grupo = ".$this->codGrupo.";";

          $update.="
                    INSERT INTO patrimonio.grupo_plano_analitica
                        (cod_grupo, cod_natureza, exercicio, cod_plano)
                    VALUES
                        (".$this->codGrupo.",".$this->codNatureza.",'".$this->anoE."','".$this->codPlano."');";

     }
     if ($dbConfig->executaSql($update)) {
      return true;
     } else {
      return false;
     }
/*
        if ($dbConfig->executaSql($update)) {
             if ($dbConfig->executaSql($update2)) {
             return true;
             } else {
                return false;
             }
        } else {
            return false;
        }
*/
        $dbConfig->fechaBd();
      }
/**************************************************************************/
/**** Método que faz a deleção dos grupos                               ***/
/**************************************************************************/
    public function deleteGrupo()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = "DELETE FROM patrimonio.grupo_plano_analitica WHERE cod_grupo = ".$this->codGrupo." AND cod_natureza = ".$this->codNatureza." AND exercicio = ".$this->anoE." AND cod_plano = ".$this->codPlano;

        $update2 = "DELETE FROM patrimonio.grupo WHERE cod_grupo = ".$this->codGrupo." AND cod_natureza = ".$this->codNatureza;
        if ($dbConfig->executaSql($update)) {
            if ($dbConfig->executaSql($update2)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
        $dbConfig->fechaBd();
    }

/**************************************************************************/
/**** Método que pega as variaveis para tratamento                      ***/
/**************************************************************************/
    public function setaVariaveisEspecie($codGrupo, $codNatureza, $codEspecie, $nomEspecie="")
    {
        $this->codNatureza = $codNatureza;
        $this->codGrupo = $codGrupo;
        $this->nomEspecie = $nomEspecie;
        $this->codEspecie = $codEspecie;
}//Fim método

/**************************************************************************/
/**** Método que faz a inserção dos Grupos                              ***/
/**************************************************************************/
    public function insereEspecie()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO patrimonio.especie (cod_especie, cod_grupo, cod_natureza, nom_especie) VALUES (".$this->codEspecie.",".$this->codGrupo.",".$this->codNatureza.",'".$this->nomEspecie."')";
        //print $insert;
    if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que faz a alteração das naturezas                          ***/
/**************************************************************************/
    public function updateEspecie()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = "UPDATE patrimonio.especie SET nom_especie = '".$this->nomEspecie."' WHERE cod_especie = ".$this->codEspecie." AND cod_natureza = ".$this->codNatureza." AND cod_grupo = ".$this->codGrupo;
    if ($dbConfig->executaSql($update))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Método que faz a deleção dos grupos                               ***/
/**************************************************************************/
    public function deleteEspecie()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = "DELETE FROM patrimonio.especie WHERE cod_especie = ".$this->codEspecie." AND cod_grupo = ".$this->codGrupo." AND cod_natureza = ".$this->codNatureza;
    if ($dbConfig->executaSql($update))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que pega as variaveis para tratamento                      ***/
/**************************************************************************/
    public function setaVariaveisAtributos($codAtributo, $nomAtributo="", $tipo="", $valorPadrao="")
    {
        $this->codAtributo = $codAtributo;
        $this->nomAtributo = $nomAtributo;
        $this->tipo = $tipo;
        $this->valorPadrao = $valorPadrao;

}//Fim método

/**************************************************************************/
/**** Método que faz a inserção dos Atributos                           ***/
/**************************************************************************/
    public function insereAtributos()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO administracao.atributo_dinamico (cod_atributo, nom_atributo, tipo, valor_padrao) VALUES ('".$this->codAtributo."','".$this->nomAtributo."','".$this->tipo."','".$this->valorPadrao."')";
        //print $insert;
    if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que faz a alteração dos Atributos                          ***/
/**************************************************************************/
    public function updateAtributo()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = "UPDATE administracao.atributo_dinamico SET nom_atributo = '".$this->nomAtributo."', tipo = '".$this->tipo."', valor_padrao = '".$this->valorPadrao."' WHERE cod_atributo = ".$this->codAtributo;
    if ($dbConfig->executaSql($update))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que faz a exclusão dos Atributos                           ***/
/**************************************************************************/
    public function deleteAtributo()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $update = "DELETE FROM administracao.atributo_dinamico WHERE cod_atributo = ".$this->codAtributo;

    if ($dbConfig->executaSql($update))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Método que pega as variaveis para tratamento                      ***/
/**************************************************************************/
    public function setaVariaveisAtributosEspecie($codAtributo, $codEspecie, $codGrupo, $codNatureza)
    {
        $this->codAtributo = $codAtributo;
        $this->codEspecie = $codEspecie;
        $this->codGrupo = $codGrupo;
        $this->codNatureza = $codNatureza;
}//Fim método

/**************************************************************************/
/**** Método que faz a inserção dos Atributos                           ***/
/**************************************************************************/
    public function insereAtributosEspecie()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO patrimonio.especie_atributo (cod_atributo,cod_especie,cod_natureza,cod_grupo) VALUES ('".$this->codAtributo."','".$this->codEspecie."','".$this->codNatureza."','".$this->codGrupo."')";
        if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }
/**************************************************************************/
/**** Método que faz a Deleção dos Atributos                           ***/
/**************************************************************************/
    public function updateAtributosEspecie($sQuery)
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = $sQuery;
        //print $insert;
    if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

/**************************************************************************/
/**** Método que faz a Deleção dos Atributos Especie                    ***/
/**************************************************************************/
    public function deleteEspecieAtributos()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        //$delete =   "delete from patrimonio.bem_atributo_especie where cod_especie = $this->codEspecie";
        echo "codBem = ".$this->codBem."<br>";
        echo "codAtributo = ".$this->codAtributo."<br>";
        echo "codNatureza = ".$this->codNatureza."<br>";
        echo "codGrupo = ".$this->codGrupo."<br>";
        echo "codEspecie = ".$this->codEspecie."<br>";
        $insert = "delete from patrimonio.especie_atributo where cod_especie = $this->codEspecie and cod_grupo = $this->codGrupo
        and cod_natureza = $this->codNatureza";
        //print $insert;
    if ($dbConfig->executaSql($insert))
            return true;
        else
            return false;
        $dbConfig->fechaBd();
        }

}//Fim Classe
