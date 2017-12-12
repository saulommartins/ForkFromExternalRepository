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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00, uc-02.02.02, uc-03.01.04
*/

class buscaPlanoConta
{
    public $codConta, $exercicio, $nomConta, $mascara, $flagPlano,
        $codNivel1, $codNivel2, $codNivel3, $codNivel4, $codNivel5,
        $codNivel6, $codNivel7, $codNivel8, $codNivel9;

/**************************************************************************
 Método construtor. Inicializa as variáveis de classe com valor nulo.
***************************************************************************/
    public function buscaPlanoConta($codConta="", $exercicio="", $nomConta="")
    {
        $this->codConta = $codConta;
        $this->exercicio = $exercicio;
        $this->nomConta = $nomConta;
        $this->mascara = pegaConfiguracao("masc_plano_contas",9,$this->exercicio);
        $this->flagPlano = false;
        $this->codNivel1 = 0;
        $this->codNivel2 = 0;
        $this->codNivel3 = 0;
        $this->codNivel4 = 0;
        $this->codNivel5 = 0;
        $this->codNivel6 = 0;
        $this->codNivel7 = 0;
        $this->codNivel8 = 0;
        $this->codNivel9 = 0;

        if (strlen($codConta) > 0) {
            $conta = explode(".",$codConta);
            $this->codNivel1 = $conta[0];
            $this->codNivel2 = $conta[1];
            $this->codNivel3 = $conta[2];
            $this->codNivel4 = $conta[3];
            $this->codNivel5 = $conta[4];
            $this->codNivel6 = $conta[5];
            $this->codNivel7 = $conta[6];
            $this->codNivel8 = $conta[7];
            $this->codNivel9 = $conta[8];
        }
    }//Fim do método construtor

/**************************************************************************
 Monta o filtro da query
***************************************************************************/
    public function montaFiltro()
    {
        $sql = "";
        if (strlen($this->nomConta) > 0) {
            $sql .= "And lower(pc.nom_conta) Like lower('%".$this->nomConta."%') ";
        }
        if ($this->codNivel1 > 0) {
            $sql .= "And pc.cod_nivel_1 = '".$this->codNivel1."' ";
        }
        if ($this->codNivel2 > 0) {
            $sql .= "And pc.cod_nivel_2 = '".$this->codNivel2."' ";
        }
        if ($this->codNivel3 > 0) {
            $sql .= "And pc.cod_nivel_3 = '".$this->codNivel3."' ";
        }
        if ($this->codNivel4 > 0) {
            $sql .= "And pc.cod_nivel_4 = '".$this->codNivel4."' ";
        }
        if ($this->codNivel5 > 0) {
            $sql .= "And pc.cod_nivel_5 = '".$this->codNivel5."' ";
        }
        if ($this->codNivel6 > 0) {
            $sql .= "And pc.cod_nivel_6 = '".$this->codNivel6."' ";
        }
        if ($this->codNivel7 > 0) {
            $sql .= "And pc.cod_nivel_7 = '".$this->codNivel7."' ";
        }
        if ($this->codNivel8 > 0) {
            $sql .= "And pc.cod_nivel_8 = '".$this->codNivel8."' ";
        }
        if ($this->codNivel9 > 0) {
            $sql .= "And pc.cod_nivel_9 = '".$this->codNivel9."' ";
        }

    return $sql;
    }//Fim da function montaFiltro

/**************************************************************************
 Procura as contas de receita de acordo com os parâmetros passados e
 retorna o resultado em uma matriz
***************************************************************************/
    public function retornaContas()
    {
        if ($this->flagPlano) {
            $sql = "Select pa.cod_plano, pc.nom_conta,
                (pc.cod_nivel_1 || '.' ||
                pc.cod_nivel_2 || '.' ||
                pc.cod_nivel_3 || '.' ||
                pc.cod_nivel_4 || '.' ||
                pc.cod_nivel_5 || '.' ||
                pc.cod_nivel_6 || '.' ||
                pc.cod_nivel_7 || '.' ||
                pc.cod_nivel_8 || '.' ||
                pc.cod_nivel_9) as conta
                From sw_plano_analitica as pa, sw_plano_conta as pc
                Where pc.exercicio = '".$this->exercicio."'
                And pc.exercicio = pa.exercicio
                And pc.cod_nivel_1 = pa.cod_nivel_1
                And pc.cod_nivel_2 = pa.cod_nivel_2
                And pc.cod_nivel_3 = pa.cod_nivel_3
                And pc.cod_nivel_4 = pa.cod_nivel_4
                And pc.cod_nivel_5 = pa.cod_nivel_5
                And pc.cod_nivel_6 = pa.cod_nivel_6
                And pc.cod_nivel_7 = pa.cod_nivel_7
                And pc.cod_nivel_8 = pa.cod_nivel_8
                And pc.cod_nivel_9 = pa.cod_nivel_9 ";
        } else {
            $sql = "Select pc.nom_conta,
                (pc.cod_nivel_1 || '.' ||
                pc.cod_nivel_2 || '.' ||
                pc.cod_nivel_3 || '.' ||
                pc.cod_nivel_4 || '.' ||
                pc.cod_nivel_5 || '.' ||
                pc.cod_nivel_6 || '.' ||
                pc.cod_nivel_7 || '.' ||
                pc.cod_nivel_8 || '.' ||
                pc.cod_nivel_9) as conta
                From sw_plano_conta as pc
                Where pc.exercicio = '".$this->exercicio."' ";
        }

        $order = " Order by pc.cod_nivel_1, pc.cod_nivel_2, pc.cod_nivel_3, pc.cod_nivel_4,
                 pc.cod_nivel_5, pc.cod_nivel_6, pc.cod_nivel_7, pc.cod_nivel_8, pc.cod_nivel_9 ";

        //Monta o filtro da query
        $sqlAux = $this->montaFiltro();

        $sql = $sql.$sqlAux.$order;

        //Pega os dados encontrados em uma query
        $conn = new dataBase;
        $conn->abreBD();
        $conn->abreSelecao($sql);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
            $vet = array();
            if ($conn->numeroDeLinhas==0) {
                $vet[0] = false;
            } else {
                $vet[0] = true;
            }

            while (!$conn->eof()) {
                $codPlano = 0;
                if ($this->flagPlano) {
                    $codPlano = $conn->pegaCampo("cod_plano");
                }
                $nomConta = $conn->pegaCampo("nom_conta");
                $conta = $conn->pegaCampo("conta");
                $valida = validaMascara($this->mascara,$conta);
                if ($valida[0]) {
                    $conta = $valida[1];
                }
                $conn->vaiProximo();
                $vetAux = array();
                $vetAux[codPlano] = $codPlano;
                $vetAux[nomConta] = $nomConta;
                $vetAux[conta] = $conta;
                $vet[] = $vetAux;
            }
        $conn->limpaSelecao();

        return $vet;
    }

}//Fim da classe

?>
